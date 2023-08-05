@extends('layouts.user_type.auth')

@section('content')
    <!-- Add the search bar HTML here -->
    <div class="search-container">
        <input type="text" id="searchInput" class="search-input" placeholder="Search students" oninput="searchStudents()">
        <div class="search-results" id="searchResults">
            <!-- Display search results here -->
        </div>
    </div>
@endsection

@push('dashboard')
<style>
    .search-container {
        position: relative;
        width: 100%;
        max-width: 400px;
    }

    .search-input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 100;
        width: 100%;
        max-height: 200px; /* Set a fixed height for the search results container */
        overflow-y: auto; /* Enable vertical scrolling for the search results */
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .search-results div {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        cursor: pointer;
    }

    .search-results div:last-child {
        border-bottom: none;
    }

    .search-results div:hover {
        background-color: #f9f9f9;
    }
</style>

<script>
    function searchStudents() {
        var searchQuery = document.getElementById('searchInput').value;

        // Send an AJAX request to the server to search for students
        var url = '/search/students?query=' + encodeURIComponent(searchQuery);
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Display search results
                var resultsDiv = document.getElementById('searchResults');
                resultsDiv.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(student => {
                        var resultItem = document.createElement('div');
                        resultItem.textContent = student.full_name; // Assuming 'full_name' is the correct property to display the student's name
                        resultsDiv.appendChild(resultItem);
                    });
                    resultsDiv.style.display = 'block'; // Show the search results container
                } else {
                    resultsDiv.style.display = 'none'; // Hide the search results container if no results found
                }
            })
            .catch(error => {
                console.error('Error searching students:', error);
            });
    }
</script>
@endpush
