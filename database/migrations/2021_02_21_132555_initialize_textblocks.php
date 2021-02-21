<?php

use App\Repository\TextBlockRepository;
use Illuminate\Database\Migrations\Migration;

class InitializeTextblocks extends Migration
{
    private TextBlockRepository $textRepo;

    public function __construct()
    {
        $this->textRepo = app(TextBlockRepository::class);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->textRepo->initialize();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
