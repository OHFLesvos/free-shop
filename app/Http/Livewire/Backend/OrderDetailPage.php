<?php

namespace App\Http\Livewire\Backend;

use App\Actions\CompleteOrder;
use App\Actions\ReadyOrder;
use App\Actions\RejectOrder;
use App\Exceptions\PhoneNumberBlockedByAdminException;
use App\Models\Order;
use App\Repository\TextBlockRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderDetailPage extends BackendPage
{
    use AuthorizesRequests;

    public Order $order;

    public string $newStatus;

    public bool $showChangeStatus = false;

    public ?string $message = '';

    public string $tab = 'products';

    protected $queryString = [
        'tab' => ['except' => 'products'],
    ];

    public array $tabs = [
        'products' => 'Products',
        'history' => 'History',
    ];

    protected function title(): string
    {
        return 'Order #' . $this->order->id;
    }

    public function mount(): void
    {
        $this->authorize('view', $this->order);

        $this->newStatus = $this->order->status;
    }

    public function getStatusesProperty(): array
    {
        $statuses = [$this->order->status];
        if ($this->order->status == 'new') {
            $statuses[] = 'ready';
            $statuses[] = 'completed';
            $statuses[] = 'cancelled';
        } elseif ($this->order->status == 'ready') {
            $statuses[] = 'completed';
            $statuses[] = 'cancelled';
        }
        return $statuses;
    }

    public function render(): View
    {
        return parent::view('livewire.backend.order-detail-page');
    }

    public function submit(): void
    {
        $this->authorize('update', $this->order);

        if ($this->order->status != $this->newStatus) {
            try {
                if ($this->newStatus == 'cancelled') {
                    RejectOrder::run($this->order);
                } elseif ($this->newStatus == 'ready') {
                    ReadyOrder::run($this->order);
                } elseif ($this->newStatus == 'completed') {
                    CompleteOrder::run($this->order);
                }
            } catch (\Twilio\Exceptions\TwilioException | PhoneNumberBlockedByAdminException $ex) {
                Log::warning('[' . get_class($ex) . '] Unable to notify customer about order change: ' . $ex->getMessage());
            } catch (\Exception $ex) {
                session()->flash('error', $ex->getMessage());
                return;
            }

            $this->order->refresh();

            $this->emitTo('backend.order-history', '$refresh');

            session()->flash('message', 'Order marked as ' . $this->newStatus . '.');
        }

        $this->showChangeStatus = false;
    }

    public function getHasMessageProperty(): bool
    {
        return $this->newStatus != $this->order->status && in_array($this->newStatus, ['ready', 'cancelled']);
    }

    public function updatedNewStatus(): void
    {
        $this->message = $this->getConfiguredMessageProperty();
    }

    public function getConfiguredMessageProperty(): ?string
    {
        $textRepo = app()->make(TextBlockRepository::class);
        if ($this->newStatus == 'ready') {
            return $textRepo->getPlain($this->getMessageTextBlockNameProperty(), optional($this->order->customer)->locale);
        }
        if ($this->newStatus == 'cancelled') {
            return $textRepo->getPlain($this->getMessageTextBlockNameProperty(), optional($this->order->customer)->locale);
        }
        return null;
    }

    public function getMessageTextBlockNameProperty(): ?string
    {
        if ($this->newStatus == 'ready') {
            return 'message-order-ready';
        }
        if ($this->newStatus == 'cancelled') {
            return 'message-order-cancelled';
        }
        return null;
    }
}
