<div>
    <h1>Products</h1>
    <table class="table">
        <thead>
            <th>Name</th>
            <th>Category</th>
            <th>Description</th>
            <th class="text-right">Stock</th>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category }}</td>
                    <td>{{ $product->description }}</td>
                    <td class="text-right">{{ $product->stock }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
