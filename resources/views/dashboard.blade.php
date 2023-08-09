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
            </div>
        </div>
    </div>
</div>


<div class="container mt-auto mb-4">
    <div class="row">
        <div class="col-12">
            <div class="card mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Attendance</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        ID
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Name
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Year Level
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Course
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('dashboard')
<style>
    
    .search-container {
        position: relative;
        text-align: center;
    }

    .search-results {
        position: absolute;
        z-index: 999;
        background-color: white;
        border: 1px solid #ccc;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
        max-height: 400px;
        overflow-y: auto;
        display: none;
        margin-top: 2px; 
    }

    .search-results .result-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        cursor: pointer;
    }

    .search-results .result-item:hover {
        background-color: #f5f5f5;
    }
</style>

<script>
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', searchStudents);
    searchInput.addEventListener('blur', hideResultsOnBlur);

    function searchStudents() {
        var searchQuery = searchInput.value;

        // Send an AJAX request to the server to search for students
        var url = '/search/students?query=' + encodeURIComponent(searchQuery);
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Display search results
                searchResults.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(student => {
                        var resultItem = document.createElement('div');
                        resultItem.classList.add('result-item');
                        var yearLevelText = student.year_level === '1' ? '1st Year' : 
                                            student.year_level === '2' ? '2nd Year' :
                                            student.year_level === '3' ? '3rd Year' :
                                            student.year_level === '4' ? '4th Year' : student.year_level;
                        resultItem.innerHTML = `
                            <div>
                                ${student.id_no} <strong>${student.full_name}</strong>, Year Level: ${yearLevelText}
                            </div>
                            <button class="btn btn-primary btn-sm">Sign In</button>
                        `;
                        
                        searchResults.appendChild(resultItem);
                    });
                    searchResults.style.display = 'block';
                } else {
                    searchResults.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error searching students:', error);
            });
    }

    function hideResultsOnBlur() {
        // Hide search results when the input loses focus
        searchResults.style.display = 'none';
    }
</script>

@endpush
