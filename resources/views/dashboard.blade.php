@extends('layouts.user_type.auth')

@section('content')

<div class="d-flex justify-content-center align-items-center">
    <div class="col-12 col-md-6">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <div class="search-container">
                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Search students"
                        oninput="searchStudents()"
                    >
                    <div class="search-results" id="searchResults"></div>
                </div>
            </div>
            <div class="card-body px-2 pt-3 pb-2">
                <!-- Your card body content goes here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('dashboard')
<style>
    /* Add your custom styling here */
    .search-container {
        position: relative;
        text-align: center;
    }

    .search-results {
        position: absolute;
        background-color: white;
        border: 1px solid #ccc;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
        max-height: 400px;
        overflow-y: auto;
        display: none;
        margin-top: 2px; /* Add margin-top to separate search bar and results */
    }

    .search-results div {
        padding: 10px;
        cursor: pointer;
    }

    .search-results div:hover {
        background-color: #f5f5f5;
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
                        resultItem.innerHTML = `<strong>${student.id_no}</strong> - ${student.full_name}, Major: ${student.major}`;
                        resultItem.addEventListener('click', () => {
                            // Handle click on result item
                            // Example: window.location.href = '/students/' + student.id;
                        });
                        resultsDiv.appendChild(resultItem);
                    });
                    resultsDiv.style.display = 'block';
                } else {
                    resultsDiv.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error searching students:', error);
            });
    }
</script>
@endpush
