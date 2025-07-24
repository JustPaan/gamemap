<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        :root {
            --bg-dark: #121212;
            --bg-darker: #1e1e1e;
            --text-primary: #ffffff;
            --text-secondary: #b3b3b3;
            --accent-color: #4a76fd;
            --border-color: #333333;
            --input-bg: #2d2d2d;
            --hover-color: #3a3a3a;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .profile-edit-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px 15px;
            background-color: var(--bg-darker);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .edit-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .profile-edit-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-size: 0.95rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .form-input {
            padding: 10px 12px;
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 0.95rem;
            width: 100%;
            box-sizing: border-box;
        }

        .form-input::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        .textarea {
            min-height: 80px;
            resize: vertical;
        }

        /* Improved Select Dropdown Styles */
        .select-input {
            padding: 10px 12px;
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 0.95rem;
            width: 100%;
            min-height: 120px; /* Make the dropdown taller */
            box-sizing: border-box;
        }

        .select-input option {
            padding: 8px 12px;
            background-color: var(--input-bg);
            color: var(--text-primary);
        }

        .select-input option:hover {
            background-color: var(--accent-color);
        }

        .select-input option:checked {
            background-color: var(--accent-color);
            color: white;
        }

        .select-input:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        /* Multi-select helper text */
        .select-helper {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        /* File input styles remain the same */
        .file-input-wrapper {
            position: relative;
            width: 100%;
        }

        .file-input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        .file-input-label {
            display: block;
            padding: 10px 12px;
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-secondary);
            font-size: 0.95rem;
            cursor: pointer;
            text-align: left;
        }

        .avatar-container {
            display: inline-block;
            margin: 0 auto;
        }

        .avatar-preview {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            display: none;
        }

        .platform-group {
            margin-top: 20px;
        }

        .toggle-platforms {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 6px;
            width: 100%;
        }

        .platform-options {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: var(--input-bg);
            display: none;
        }

        .platform-option {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px 0;
        }

        .platform-option input {
            margin-right: 8px;
        }

        .platform-label {
            font-size: 0.95rem;
        }

        .form-submit-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }

        .back-btn {
            flex: 1;
            padding: 12px;
            font-weight: 500;
            font-size: 1rem;
            background-color: #3a3a3a;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            transition: background-color 0.2s;
        }

        .back-btn:hover {
            background-color: #2e2e2e;
        }

        .submit-btn, .home-btn, .cancel-btn {
            flex: 1;
            padding: 12px;
            font-weight: 500;
            font-size: 1rem;
            border: none;
            border-radius: 6px;
            text-align: center;
            transition: background-color 0.2s;
        }

        .submit-btn {
            background-color: var(--accent-color);
            color: white;
        }

        .submit-btn:hover {
            background-color: #3a6ae8;
        }

        .home-btn {
            background-color: #3a3a3a;
            color: white;
            text-decoration: none;
            line-height: 2.5rem;
        }

        .home-btn:hover {
            background-color: #2e2e2e;
        }

        .cancel-btn {
            background-color: var(--hover-color);
            color: white;
            text-decoration: none;
            line-height: 2.5rem;
        }

        .cancel-btn:hover {
            background-color: #2e2e2e;
        }

        @media (max-width: 480px) {
            .profile-edit-container {
                padding: 15px 12px;
            }

            .edit-title {
                font-size: 1.3rem;
                margin-bottom: 15px;
            }

            .profile-edit-form {
                gap: 12px;
            }

            .form-input {
                padding: 8px 10px;
            }

            .form-submit-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="profile-edit-container">
    <h2 class="edit-title">Edit Profile</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-edit-form">
        @csrf

        <!-- Profile Picture / Avatar -->
        <div class="form-group" style="text-align: center;">
            <label class="form-label">Profile Picture</label>
            <div class="avatar-container" style="margin: 0 auto; cursor: pointer; display: inline-block;">
                @if($user->avatar)
                    <img id="avatar-preview" class="avatar-preview" alt="Avatar Preview" src="{{ $user->avatar_url }}" onclick="document.getElementById('avatar-upload').click()" style="display: block; width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color); cursor: pointer;">
                    <div class="avatar-placeholder" onclick="document.getElementById('avatar-upload').click()" style="width: 60px; height: 60px; border-radius: 50%; background-color: #e0e0e0; display: none; justify-content: center; align-items: center; border: 2px solid var(--border-color);">
                        <span class="placeholder-icon" style="font-size: 30px; color: var(--text-secondary);">👤</span>
                    </div>
                @else
                    <img id="avatar-preview" class="avatar-preview" alt="Avatar Preview" src="path/to/default/avatar.png" onclick="document.getElementById('avatar-upload').click()" style="display: none; width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color); cursor: pointer;">
                    <div class="avatar-placeholder" onclick="document.getElementById('avatar-upload').click()" style="width: 60px; height: 60px; border-radius: 50%; background-color: #e0e0e0; display: flex; justify-content: center; align-items: center; border: 2px solid var(--border-color);">
                        <span class="placeholder-icon" style="font-size: 30px; color: var(--text-secondary);">👤</span>
                    </div>
                @endif
                <input type="file" name="avatar" accept="image/*" class="file-input" id="avatar-upload" style="display: none;">
            </div>
            <p class="text-secondary" style="text-align: center;">Click on the image to change your profile picture</p>
        </div>

        <!-- Full Name -->
        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-input" placeholder="Enter full name" value="{{ old('name', $user->name) }}" required>
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-input" placeholder="Enter your email address" value="{{ old('email', $user->email) }}" required>
        </div>

        <!-- Bio -->
        <div class="form-group">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-input textarea" rows="3" placeholder="Enter bio" required>{{ old('bio', $user->bio) }}</textarea>
        </div>

        <!-- Improved Games Dropdown -->
        <div class="form-group">
            <label class="form-label">Favorite Games</label>
            <select name="games[]" class="form-input select-input" multiple required>
                @foreach($games as $game)
                    <option value="{{ $game->name }}" 
                        {{ in_array($game->name, old('games', json_decode($user->games ?? '[]', true) ?? [])) ? 'selected' : '' }}>
                        {{ $game->name }}
                    </option>
                @endforeach
            </select>
            <p class="select-helper">Hold Ctrl/Cmd to select multiple games</p>
        </div>

        <!-- Platforms -->
        <div class="form-group platform-group">
            <label class="form-label">Platforms</label>
            <button type="button" class="toggle-platforms" onclick="togglePlatforms()">Choose Platforms</button>
            <div class="platform-options" style="display: none;">
                <label class="platform-option">
                    <input type="checkbox" name="platforms[]" value="Console" {{ in_array('Console', old('platforms', explode(',', $user->platforms ?? ''))) ? 'checked' : '' }}>
                    <span class="platform-label">Console</span>
                </label>
                <label class="platform-option">
                    <input type="checkbox" name="platforms[]" value="PC" {{ in_array('PC', old('platforms', explode(',', $user->platforms ?? ''))) ? 'checked' : '' }}>
                    <span class="platform-label">PC</span>
                </label>
                <label class="platform-option">
                    <input type="checkbox" name="platforms[]" value="Mobile" {{ in_array('Mobile', old('platforms', explode(',', $user->platforms ?? ''))) ? 'checked' : '' }}>
                    <span class="platform-label">Mobile</span>
                </label>
            </div>
        </div>

        <!--Back, Submit, Home, and Cancel Buttons -->
        <div class="form-submit-buttons">
            <a href="{{ route('profile.info') }}" class="back-btn">Back to Profile Info</a>
            <button type="submit" class="submit-btn">Save Changes</button>
            <a href="{{ route('home') }}" class="home-btn">🏠 Home</a>
            <a href="{{ url()->previous() }}" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>

<script>
    // Update file input label and display the image
    document.querySelectorAll('.file-input').forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.querySelector('.avatar-placeholder');

            if (this.files && this.files.length > 0) {
                const file = this.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                };

                reader.readAsDataURL(file);
            } else {
                // If no file selected, show existing avatar or placeholder
                const hasExistingAvatar = preview.src && !preview.src.includes('path/to/default/avatar.png');
                if (hasExistingAvatar) {
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                } else {
                    preview.style.display = 'none';
                    placeholder.style.display = 'flex';
                }
            }
        });
    });

    function togglePlatforms() {
        const platforms = document.querySelector('.platform-options');
        platforms.style.display = platforms.style.display === "none" || platforms.style.display === "" ? "block" : "none";
    }
</script>

</body>
</html>