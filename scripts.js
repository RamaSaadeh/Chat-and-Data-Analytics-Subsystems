// SIDEBAR TOGGLE

let sidebarOpen = false;
const sidebar = document.getElementById('sidebar');

function openSidebar() {
  if (!sidebarOpen) {
    sidebar.classList.add('sidebar-responsive');
    sidebarOpen = true;
  }
}

function closeSidebar() {
  if (sidebarOpen) {
    sidebar.classList.remove('sidebar-responsive');
    sidebarOpen = false;
  }
}

// ---------- CHARTS ----------

// BAR CHART
const barChartOptions = {
  series: [
    {
      data: [10, 8, 6],
      name: 'Products',
    },
  ],
  chart: {
    type: 'bar',
    background: 'transparent',
    height: 350,
    toolbar: {
      show: false,
    },
  },
  colors: ['#2962ff', '#d50000', '#2e7d32'],
  plotOptions: {
    bar: {
      distributed: true,
      borderRadius: 4,
      horizontal: false,
      columnWidth: '40%',
    },
  },
  dataLabels: {
    enabled: false,
  },
  fill: {
    opacity: 1,
  },
  grid: {
    borderColor: '#55596e',
    yaxis: {
      lines: {
        show: true,
      },
    },
    xaxis: {
      lines: {
        show: true,
      },
    },
  },
  legend: {
    labels: {
      colors: '#000000',
    },
    show: true,
    position: 'top',
  },
  stroke: {
    colors: ['transparent'],
    show: true,
    width: 2,
  },
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'dark',
  },
  xaxis: {
    categories: ['Admin', 'Manager', 'Employee'],
    title: {
      style: {
        color: '#000000',
      },
    },
    axisBorder: {
      show: true,
      color: '#55596e',
    },
    axisTicks: {
      show: true,
      color: '#55596e',
    },
    labels: {
      style: {
        colors: '#000000',
      },
    },
  },
  yaxis: {
    title: {
      text: 'Count',
      style: {
        color: '#000000',
      },
    },
    axisBorder: {
      color: '#55596e',
      show: true,
    },
    axisTicks: {
      color: '#55596e',
      show: true,
    },
    labels: {
      style: {
        colors: '#000000',
      },
    },
  },
};

const barChart = new ApexCharts(
  document.querySelector('#bar-chart'),
  barChartOptions
);
barChart.render();

// AREA CHART

var options = {
  series: [44, 55, 41, 17],
  chart: {
  type: 'donut',
},
labels: ['In Progress','Completed','Not Started','Overdue'],
responsive: [{
  breakpoint: 480,
  options: {
    chart: {
      width: 200
    },
    legend: {
      position: 'bottom'
     
    }
  }
}]
};

var chart = new ApexCharts(document.querySelector("#area-chart"), options);
chart.render();

//countries

var options = {
  series: [{
  data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
}],
  chart: {
  type: 'bar',
  height: 250
},
plotOptions: {
  bar: {
    borderRadius: 4,
    borderRadiusApplication: 'end',
    horizontal: true,
  }
},
dataLabels: {
  enabled: false
},
xaxis: {
  categories: ['South Korea', 'Canada', 'United Kingdom', 'Netherlands', 'Italy', 'France', 'Japan',
    'United States', 'China', 'Germany'
  ],
}
};

var chart = new ApexCharts(document.querySelector("#countries-chart"), options);
chart.render();

//tasks
var options = {
  series: [

  {
    name: 'In Progress',
    group: 'actual',
    data: [48000, 50000, 40000, 65000, 25000]
  },
  {
    name: 'Overdue',
    group: 'budget',
    data: [13000, 36000, 20000, 8000, 13000]
  },
  {
    name: 'Completed',
    group: 'actual',
    data: [20000, 40000, 25000, 10000, 12000]
  }
],
  chart: {
  type: 'bar',
  height: 350,
  stacked: true,
},
stroke: {
  width: 1,
  colors: ['#fff']
},
dataLabels: {
  formatter: (val) => {
    return val / 1000 + 'K'
  }
},
plotOptions: {
  bar: {
    horizontal: true
  }
},
xaxis: {
  categories: [
    'Project 5',
    'Project 4',
    'Project 3',
    'Project 2',
    'Project 1'
  ],
  labels: {
    formatter: (val) => {
      return val / 1000 + 'K'
    }
  }
},
fill: {
  opacity: 1,
},
colors: ['#80c7fd', '#008FFB', '#80f1cb', '#00E396'],
legend: {
  position: 'top',
  horizontalAlign: 'left'
}
};

var chart = new ApexCharts(document.querySelector("#tasks-chart"), options);
chart.render();