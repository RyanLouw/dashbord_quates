let salesData = [];

function getTimeColor(createdTime) {
  const ONE_HOUR = 60 * 60 * 1000; // in milliseconds
  const currentTime = new Date();
  const saleTime = new Date(createdTime);
  const timeDiff = currentTime.getTime() - saleTime.getTime();

  if (timeDiff <= ONE_HOUR) {
    return 'white';
  } else if (timeDiff <= 2 * ONE_HOUR) {
    return 'yellow';
  } else {
    return 'red';
  }
}


// function to add sales data to a sales card
function addSalesData(cardId, salesData) {
  const card = document.getElementById(cardId);
  const salesList = card.querySelector('ul');
  salesList.innerHTML = '';
  salesData.forEach(data => {
    const li = document.createElement('li');
    const saleTime = new Date(`${data.created_date} ${data.created_time}`);
    const timeElapsed = new Date().getTime() - saleTime.getTime();
    const timeElapsedHours = Math.floor(timeElapsed / (1000 * 60 * 60));
    const timeElapsedColor = getTimeElapsedColor(timeElapsedHours);
    const timeText = document.createTextNode(`${data.created_time} `);
    const saleNumberText = document.createTextNode(data.quote_no);
    li.appendChild(timeText);
    li.appendChild(document.createTextNode(' '));
    li.appendChild(saleNumberText);
    li.style.backgroundColor = timeElapsedColor;
  
    
    salesList.appendChild(li);
  });
}

function getTimeElapsedColor(hours) {
  if (hours <= 1) {
    return '#5c5959';
  } else if (hours <= 2) {
    return 'black';
  } else if (hours <= 3) {
    return '#848a20';
  } 
   else {
    return '#7d2020';
  }
}



// function to filter sales data by branch and date
function filterSalesData(salesData, branchCode) {
  const today = new Date().toISOString().split('T')[0];
  //console.log(salesData);
  return salesData.filter(data => {
    return data.branch_code === branchCode && data.created_date === today;
   
  });
  
}

// function to generate sales person cards
function generateSalesPersonCards(salesData) {
  const salesCards = document.querySelector('.sales-cards');
  salesCards.innerHTML = '';
  const salespeople = [...new Set(salesData.map(data => data.salesman_name.split(' ')[0]))];
  const salespersonSales = salespeople.map(salesperson => {
    const filteredData = salesData.filter(data => data.salesman_name.split(' ')[0] === salesperson);
    return {
      name: salesperson,
      sales: filteredData.length
    };
  });
  const sortedSalespersonSales = salespersonSales.sort((a, b) => a.sales - b.sales);
  sortedSalespersonSales.forEach(salesperson => {
    const filteredData = salesData.filter(data => data.salesman_name.split(' ')[0] === salesperson.name);
    const card = document.createElement('div');
    card.classList.add('sales-card');
    card.id = `sales-card-${salesperson.name}`;
    const heading = document.createElement('h2');
    heading.innerText = salesperson.name;
    const salesList = document.createElement('ul');
    card.appendChild(heading);
    card.appendChild(salesList);
    salesCards.appendChild(card);
    // check if the card variable is not null before calling addSalesData()
    if (card !== null) {
      addSalesData(card.id, filteredData);
    }
  });
}


// function to retrieve sales data from server
function getSalesData(branchCode) {
  fetch('js/dummy_sales_data.json')
    .then(response => response.json())
    .then(data => {
      salesData = data;
      const filteredData = filterSalesData(salesData, branchCode);
      generateSalesPersonCards(filteredData);
      document.querySelector('#quote-number').textContent = filteredData.length;
    })
    .catch(error => console.error(error));
}


// get the branch code from the URL parameter
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const branchCode = urlParams.get('branch');

// call getSalesData with the branch code
if (branchCode) {
  getSalesData(branchCode);
} else {
  getSalesData(1); // set default branch code here if needed
}
