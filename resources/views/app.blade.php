<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    <style>
        .hidden {
            display: none;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto my-20">

    <div>
        <div id="users-container"
             class="grid grid-cols-5 gap-4 bg-gray-100 p-4 rounded-md shadow-md dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            @foreach ($users as $user)
                <div>
                    <img src="/storage/{{$user->photo}}" alt="photo-{{ $user->name }}">
                </div>
                <div>{{ $user->name }}</div>
                <div>{{ $user->email }}</div>
                <div>{{ $user->phone }}</div>
                <div>{{ $user->position->name }}</div>
            @endforeach
        </div>

        <button id="show-more-users" data-page="1"
                class="bg-blue-500 text-white font-bold py-2 px-4 rounded mt-4 mx-auto w-max block">Show more
        </button>

    </div>

    <div class="mt-12 bg-gray-100 p-4 rounded-md shadow-md dark:bg-gray-800 dark:border-gray-600 dark:text-white">
        <form method="POST" id="register-user">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 dark:text-white font-bold mb-2">Name:</label>
                <input type="text" id="name" name="name"
                       class="form-input w-60 px-4 py-2 rounded-md border-gray-300 text-black">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 dark:text-white font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email"
                       class="form-input w-60 px-4 py-2 rounded-md border-gray-300 text-black">
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-gray-700 dark:text-white font-bold mb-2">Phone:</label>
                <input type="text" id="phone" name="phone"
                       class="w-60 px-4 py-2 form-input rounded-md border-gray-300 text-black">
            </div>

            <div class="mb-4">
                <label for="position_id" class="block text-gray-700 dark:text-white font-bold mb-2">Position:</label>
                <select name="position_id" id="position_id"
                        class="w-60 px-4 py-2 form-input rounded-md border-gray-300 text-black">
                    @foreach($positions as $position)
                        <option value="{{$position->id}}">{{$position->name}}</option>
                    @endforeach
                </select>

            </div>

            <div class="mb-4">
                <label for="photo" class="block text-gray-700 dark:text-white font-bold mb-2">Photo:</label>
                <input type="file" id="photo" name="photo" class="form-input rounded-md border-gray-300">
            </div>

            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded">Submit</button>
        </form>

        <div id="successMessage" class="mt-4 text-green-400 text-bold" style="display: none;">
            <h2>Form Submitted Successfully!</h2>
        </div>
        <div id="errors" class="mt-4 text-red-400 text-bold" style="display: none;">
        </div>
    </div>
</div>
</body>

<script>
    // Register new user
    document.getElementById('register-user').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission
        let formData = new FormData(this);

        // Get form data
        function getToken() {
            fetch('/api/v1/token')
                .then(response => response.text())
                .then(html => {
                    let data = JSON.parse(html)

                    sendForm(data.token, formData)
                })
                .catch(error => {
                    console.error(error);
                    // Handle error, show error message, etc.
                });
        }

        function sendForm(token, formData) {

            fetch('/api/v1/users', {
                method: 'POST',
                body: formData,
                headers: {
                    'Token': token
                }
            })
                .then(response => response.text())
                .then(response => {
                    var jsonResponse = JSON.parse(response);
                    if (jsonResponse.success) {
                        // Clear form fields
                        document.getElementById('name').value = '';
                        document.getElementById('email').value = '';
                        document.getElementById('phone').value = '';
                        document.getElementById('position_id').value = '';
                        document.getElementById('photo').value = '';
                        // Show success message
                        document.getElementById('successMessage').style.display = 'block';

                    } else {
                        document.getElementById('errors').style.display = 'block';
                        document.getElementById('errors').innerHTML = '<div class="text-xl">' + jsonResponse.message + '</div>';

                        if (jsonResponse.fails) {
                            for (let key in jsonResponse.fails) {
                                jsonResponse.fails[key].forEach(function (fail) {
                                    document.getElementById('errors').innerHTML += '<div>' + fail + '</div>';
                                })
                            }
                        }

                        throw new Error('Failed to submit form');
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        }

        getToken();
    });
</script>

<script>
    //Show users paginate
    document.getElementById('show-more-users').addEventListener('click', function () {
        let page = parseInt(document.getElementById('show-more-users').getAttribute('data-page')) + 1;
        fetch('/api/v1/users?page=' + page)
            .then(response => response.text())
            .then(html => {
                let users = JSON.parse(html).users
                console.log(users);
                users.forEach(function (user) {
                    document.getElementById('users-container').innerHTML +=
                        '<div><img src="' + user.photo + '" alt="photo-' + user.name + '"/></div>' +
                        '<div>' + user.name + '</div>' +
                        '<div>' + user.email + '</div>' +
                        '<div>' + user.phone + '</div>' +
                        '<div>' + user.position + '</div>';
                })
                document.getElementById('show-more-users').setAttribute('data-page', page)

                if (page >= JSON.parse(html).total_pages) {
                    document.getElementById('show-more-users').classList.add('hidden')
                }
            });
    });
</script>

</html>

