<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <div class="bg-white p-4 flex items-center justify-between shadow">
        <div class="flex items-center space-x-4">
            <a href="/organizer/dashboard" class="text-blue-500 hover:underline">
                ← Back
            </a>
            <h1 class="text-xl font-semibold text-gray-800">Organizer Profile</h1>
        </div>
        <div class="text-blue-500 text-2xl">👤</div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="max-w-4xl mx-auto mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Profile Form -->
    <div class="max-w-4xl mx-auto mt-8 bg-white p-6 rounded shadow">
        <form action="/organizer/profile/update" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left: Profile Picture -->
                <div class="space-y-4">
                    <div class="bg-gray-200 h-48 flex items-center justify-center rounded overflow-hidden">
                        @if($organizer->avatar)
                            <img id="profile-preview" src="{{ asset('storage/' . $organizer->avatar) }}" alt="Profile Picture" class="w-full h-full object-cover">
                            <span id="upload-text" class="text-gray-600 hidden">Upload Profile Picture</span>
                        @else
                            <span id="upload-text" class="text-gray-600">Upload Profile Picture</span>
                            <img id="profile-preview" src="" alt="" class="w-full h-full object-cover hidden">
                        @endif
                    </div>
                    <input type="file" name="avatar" id="profile-upload" class="w-full text-sm text-gray-600" accept="image/*">
                </div>

                <!-- Right: Organizer Info -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Username:</label>
                        <input type="text" name="username" class="w-full mt-1 p-2 border rounded bg-gray-50" 
                               value="{{ old('username', $organizer->name) }}" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email:</label>
                        <input type="email" name="email" class="w-full mt-1 p-2 border rounded bg-gray-50" 
                               value="{{ old('email', $organizer->email) }}" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number:</label>
                        <input type="tel" name="phone" class="w-full mt-1 p-2 border rounded" 
                               value="{{ old('phone', $organizer->phone) }}" placeholder="e.g. +1234567890">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bio:</label>
                        <textarea name="bio" class="w-full mt-1 p-2 border rounded" rows="3" 
                                  placeholder="Tell about yourself and include your home address here if needed">{{ old('bio', $organizer->bio) }}</textarea>
                        @error('bio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-8 flex justify-end space-x-4">
                <button type="reset" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded">RESET</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">UPDATE PROFILE</button>
            </div>
        </form>
    </div>

    <script>
        // Profile picture preview
        document.getElementById('profile-upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-preview').src = e.target.result;
                    document.getElementById('profile-preview').classList.remove('hidden');
                    document.getElementById('upload-text').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });

        // Hide success message after 5 seconds if it exists
        const successMessage = document.querySelector('[role="alert"]');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
        }
    </script>

</body>
</html>