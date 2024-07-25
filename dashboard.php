<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ticket Table</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/simplePagination.css">
  <style>
    #logo {
      max-width: 100px;
      height: auto;
    }
    .status-dropdown {
      width: 150px;
    }
    .status-completed {
      background-color: green;
      color: white;
    }
    table, th, td {
      border: 1px solid black;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      padding: 8px;
      text-align: left;
    }
    .small-search-input {
      width: 30%; /* Adjust the width as needed */
      border: 1px solid black;
    }
  </style>
</head>
<body>
<header class="bg-light py-3 text-center">
  <img id="logo" src="ve.jpeg" alt="Logo" class="mb-3">
  <h1>Welcome to the IT Ticket System</h1>
</header>
<div class="container mt-5">
  <h2>Ticket Queries</h2>
  <div class="form-group">
    <input type="text" id="searchInput" class="form-control small-search-input" placeholder="Search...">
  </div>
  <table class="table table-striped">
    <thead class="thead-dark">
      <tr>
        <th scope="col">Name</th>
        <th scope="col">Email</th>
        <th scope="col">Query</th>
        <th scope="col">Office</th>
        <th scope="col">Priority</th>
        <th scope="col">Submit Date</th>
        <th scope="col">Status</th>
        <th scope="col">Attended By</th>
      </tr>
    </thead>
    <tbody id="ticketTableBody">
      <!-- Data will be loaded here -->
    </tbody>
  </table>
  <div id="pagination-container"></div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/jquery.simplePagination.js"></script>
<script>
  // Load table data with pagination
  function loadTableData(query = '', page = 1) {
    $.ajax({
      url: 'fetch_tickets.php',
      method: 'GET',
      data: { search: query, page: page },
      success: function(data) {
        const response = JSON.parse(data);
        $('#ticketTableBody').html(response.tickets);
        $('#pagination-container').pagination({
          items: response.total,
          itemsOnPage: 10,
          currentPage: page,
          cssStyle: 'light-theme',
          onPageClick: function(pageNumber) {
            localStorage.setItem('currentPage', pageNumber); // Save current page to localStorage
            loadTableData(query, pageNumber);
          }
        });
      },
      error: function(xhr, status, error) {
        console.error('Error loading table data: ' + error);
      }
    });
  }

  // Load data on page load
  $(document).ready(function() {
    const savedPage = localStorage.getItem('currentPage') || 1; // Get saved page or default to 1
    loadTableData('', savedPage);

    // Reload data every 30 seconds
    setInterval(() => {
      const query = $('#searchInput').val();
      loadTableData(query, savedPage);
    }, 30000);

    // Search input handler
    $('#searchInput').on('input', function() {
      const query = $(this).val();
      loadTableData(query);
    });

    // Update status on change
    $(document).on('change', '.status-dropdown', function() {
      var status = $(this).val();
      var ticketId = $(this).data('ticket-id');
      alert('Status changed to: ' + status);

      // Perform AJAX update to your PHP script for updating status
      $.ajax({
        url: 'update_status.php',
        method: 'POST',
        data: { ticketId: ticketId, status: status },
        success: function(response) {
          console.log('Status updated successfully');
          const query = $('#searchInput').val();
          loadTableData(query, savedPage); // Reload table data after update
        },
        error: function(xhr, status, error) {
          console.error('Error updating status: ' + error);
        }
      });
    });
  });
</script>
</body>
</html>
