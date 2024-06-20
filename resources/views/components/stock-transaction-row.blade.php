<tr>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
        <a href="{{ route('stock-transactions.show', $transaction->stock_symbol) }}" class="text-blue-500 hover:text-blue-700">
            {{ $transaction->stock_symbol }}
        </a>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $transaction->formatted_total_quantity }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $transaction->current_price }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $transaction->formatted_total_cost_basis }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $transaction->formatted_average_price }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $transaction->formatted_market_price }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm {{ $transaction->formatted_profit_loss >= 0 ? 'text-green-500' : 'text-red-500' }}">
        {{ $transaction->formatted_profit_loss }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm {{ $transaction->formatted_profit_loss_percentage >= 0 ? 'text-green-500' : 'text-red-500' }}">
        {{ $transaction->formatted_profit_loss_percentage }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->last_transaction_date)->format('d/m/Y') }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
        <a href="{{ route('stock-transactions.show', $transaction->stock_symbol) }}" class="text-blue-500 hover:text-blue-700">
            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12h3m4 0a2 2 0 01-2 2h-3v-2m0 0V8m0 4v4m-4 4h-3v-4m0 0V8m0 4H9m4-4V4m0 4H6m4 0V4m0 4H9m4-4V4m-4 0H4m4 0H4"></path></svg>
        </a>
    </td>
</tr>
