<div>
    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    @foreach (['stock_symbol' => 'Ticket', 'total_quantity' => '# Acciones', 'current_price' => 'Precio Actual', 'total_cost_basis' => 'Base de coste', 'average_price' => 'Precio Promedio', 'formatted_market_price' => 'Precio Mercado', 'formatted_profit_loss' => 'P/G', 'formatted_profit_loss_percentage' => '% P/G', 'last_transaction_date' => 'Última Transacción'] as $field => $label)
                        <th wire:click="sortBy('{{ $field }}')" class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            {{ $label }} @if($sortField == $field) <span>{!! $sortAsc ? '&#9650;' : '&#9660;' !!}</span> @endif
                        </th>
                    @endforeach
                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Detalle
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($transactionsWithQuantity as $transaction)
                    <x-stock-transaction-row :transaction="$transaction" />
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <div x-data="{ open: false }">
            <button @click="open = !open" class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-full">
                Acciones Vendidas
            </button>
            <div x-show="open" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            @foreach (['stock_symbol' => 'Ticket', 'total_quantity' => '# Acciones', 'current_price' => 'Precio Actual', 'total_cost_basis' => 'Base de coste', 'average_price' => 'Precio Promedio', 'formatted_market_price' => 'Precio Mercado', 'formatted_profit_loss_percentage' => '% P/G', 'last_transaction_date' => 'Última Transacción'] as $field => $label)
                                <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $label }}
                                </th>
                            @endforeach
                            <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Detalle
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($transactionsWithoutQuantity as $transaction)
                            <x-stock-transaction-row :transaction="$transaction" />
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
