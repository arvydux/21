<div>
    <div  class="grid md:grid-cols-2 auto-rows-min gap-4">
        <div class="">
            <div class="flex grid content-center text-white/80 rounded-xl ">
                <div class="font-semibold text-8xl">Gaminami:</div>
            </div>
        </div>
        <div class="">
            <div class="flex grid content-center text-white/80 rounded-xl ">
                <div class="font-semibold text-8xl">Paruošti:</div>
            </div>
        </div>
    </div>
    <div  class="grid md:grid-cols-2 auto-rows-min gap-4">
        <div id="unready-orders" class="grid md:grid-cols-2 auto-rows-min gap-4 mr-8">
        </div>
        <div id="ready-orders" class="grid md:grid-cols-2 auto-rows-min gap-4  ml-8">
        </div>
    </div>
</div>
<script>
    function fetchUnreadyOrders() {
        fetch('/get-unready-orders')
            .then(response => response.json())
            .then(data => {
                const ordersDiv = document.getElementById('unready-orders');
                ordersDiv.innerHTML =``;
                data.forEach(order => {
                    const orderHTML = `
        <div wire:click="makeOrderReady('${order.number}')"  class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                aspect-video overflow-hidden rounded-xl " :class="${order.by_phone} ? 'bg-red-500/90' : 'bg-yellow-400/90'">
            <div  :class="${order.by_phone} ? 'zoom-in-out-box' : 'text-white'" class="flex grid content-center flex-col gap-2 h-full rounded-xl w-full">
                <div class=" font-semibold text-6xl">${order.number}</div>
                <div class="font-semibold text-2xl">
                    ${Math.floor((new Date() - new Date(order.created_at)) / 60000)} min.
                </div>
            </div>
        </div>
                `;
                    ordersDiv.insertAdjacentHTML('beforeend', orderHTML);
                });
            });
    }

    function fetchReadyOrders() {
        fetch('/get-ready-orders')
            .then(response => response.json())
            .then(data => {
                const ordersDiv = document.getElementById('ready-orders');
                ordersDiv.innerHTML =``;
                data.forEach(order => {
                    const orderHTML = `
        <div wire:click="makeOrderNotReady('${order.number}')"  class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                aspect-video overflow-hidden rounded-xl  bg-emerald-600" >
            <div class="flex grid content-center flex-col gap-2 h-full rounded-xl w-full">
                <div class=" font-semibold text-6xl">${order.number}</div>
            </div>
        </div>
                `;
                    ordersDiv.insertAdjacentHTML('beforeend', orderHTML);
                });
            });
    }
    // Initial fetch
    fetchUnreadyOrders();
    fetchReadyOrders();

    // Fetch orders every 5 seconds
    setInterval(fetchUnreadyOrders, 5000);
    setInterval(fetchReadyOrders, 5000);


</script>

