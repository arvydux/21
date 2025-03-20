<div>
    @if(isset($buttonName))
        @if($buttonName === 'ceburekai')
            <livewire:dynamic-component :component="'ceburekai'" :key="" :mydata="67567567"/>
        @endif
        @if($buttonName === 'kibinai')
            <livewire:dynamic-component :component="'kibinai'" :key="" :mydata="67567567"/>
        @endif
    @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
        <div class="relative gap-3 mt-10 overflow-x-auto shadow-md sm:rounded-lg text-white/80">
            <table class="w-full text-sm text-left  dark:text-gray-400">
                <thead class="text-xs uppercase bg-emerald-700/70">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Product name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Color
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Price
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach(\App\Models\SimpleProduct::all() as $productName)
                    <tr class=" border-b border-green-500/30 bg-white/10">
                        <th scope="row" class="px-6 py-4 font-medium   dark:text-white">
                            {{ $productName->name }}
                        </th>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> Online
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{ $productName->price }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

</div>
