document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('intensityChart')?.getContext('2d');
    if (!ctx || typeof intensityChartData === 'undefined') return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: intensityChartData.labels,
            datasets: [{
                label: 'Förändring av energiintensitet (%) jämfört med 2008',
                data: intensityChartData.values,
                borderColor: 'rgb(153, 102, 255)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Energiintensitet i Sverige'
                }
            }
        }
    });
});
