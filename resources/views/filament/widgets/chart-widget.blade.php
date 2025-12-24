<div class="space-y-6">
    <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-600 mb-2">Movement Per Month (last 12 months)</div>
        <canvas id="movementChart" height="120"></canvas>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-600 mb-2">Cylinder Type Distribution</div>
        <canvas id="typeChart" height="120"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function(){
            const movementLabels = @json($this->movementLabels);
            const movementData = @json($this->movementData);

            const ctx = document.getElementById('movementChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: movementLabels,
                    datasets: [{
                        label: 'Events',
                        data: movementData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.1)'
                    }]
                },
                options: {maintainAspectRatio:false}
            });

            const typeLabels = @json($this->typeLabels);
            const typeData = @json($this->typeData);
            const ctx2 = document.getElementById('typeChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        data: typeData,
                        backgroundColor: typeLabels.map((_,i)=>['#ef4444','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ec4899'][i%6])
                    }]
                },
                options: {maintainAspectRatio:false}
            });
        })();
    </script>
</div>
