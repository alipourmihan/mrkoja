'use strict';

let myIncomeChart;
let userRegisterChart;

// Function to initialize or reinitialize both charts
function initCharts() {
  const chartLabels = jalaliMonthArr;
  
  // Destroy existing charts if they exist
  if (myIncomeChart) {
    myIncomeChart.destroy();
  }
  
  if (userRegisterChart) {
    userRegisterChart.destroy();
  }
  
  // Reinitialize package purchase chart
  const chartOne = document.getElementById('packagePurchaseChart').getContext('2d');
  Chart.defaults.global.defaultFontFamily = "var(--matn), sans-serif"; // Add your desired font family
  myIncomeChart = new Chart(chartOne, {
    type: 'line',
    data: {
      labels: chartLabels,
      datasets: [{
        label: 'خرید ماهانه پکیج',
        data: packagePurchaseIncomesArr,
        borderColor: '#1d7af3',
        pointBorderColor: '#FFF',
        pointBackgroundColor: '#1d7af3',
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBorderWidth: 1,
        pointRadius: 4,
        backgroundColor: 'transparent',
        fill: true,
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: 'bottom',
        labels: {
          padding: 10,
          fontColor: '#1d7af3',
          fontFamily: "var(--matn), sans-serif" // Added font family
        }
      },
      tooltips: {
        bodySpacing: 4,
        mode: 'nearest',
        intersect: 0,
        position: 'nearest',
        xPadding: 10,
        yPadding: 10,
        caretPadding: 10
      },
      layout: {
        padding: {
          left: 15,
          right: 15,
          top: 15,
          bottom: 15
        }
      }
    }
  });
  
  // Reinitialize user registration chart
  const chartTwo = document.getElementById('userChart').getContext('2d');
  userRegisterChart = new Chart(chartTwo, {
    type: 'line',
    data: {
      labels: chartLabels,
      datasets: [{
        label: 'کاربران ثبت نام شده به تفکیک ماه',
        data: totalUsersArr,
        borderColor: '#1d7af3',
        pointBorderColor: '#FFF',
        pointBackgroundColor: '#1d7af3',
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBorderWidth: 1,
        pointRadius: 4,
        backgroundColor: 'transparent',
        fill: true,
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: 'bottom',
        labels: {
          padding: 10,
          fontColor: '#1d7af3'
        }
      },
      tooltips: {
        bodySpacing: 4,
        mode: 'nearest',
        intersect: 0,
        position: 'nearest',
        xPadding: 10,
        yPadding: 10,
        caretPadding: 10
      },
      layout: {
        padding: {
          left: 15,
          right: 15,
          top: 15,
          bottom: 15
        }
      }
    }
  });
}

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
  initCharts();
});
