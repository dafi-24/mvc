document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('energyChart')?.getContext('2d');
    if (!ctx || typeof energyChartData === 'undefined') return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: energyChartData.labels,
            datasets: [
                {
                    label: 'Värme/Industri (%)',
                    data: energyChartData.heatingIndustry,
                    borderColor: 'rgb(255, 99, 132)',
                    fill: false
                },
                {
                    label: 'El (%)',
                    data: energyChartData.electricity,
                    borderColor: 'rgb(54, 162, 235)',
                    fill: false
                },
                {
                    label: 'Transport (%)',
                    data: energyChartData.transport,
                    borderColor: 'rgb(255, 206, 86)',
                    fill: false
                },
                {
                    label: 'Totalt (%)',
                    data: energyChartData.total,
                    borderColor: 'rgb(75, 192, 192)',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Andel förnybar energi i Sverige per sektor'
                }
            }
        }
    });
});
