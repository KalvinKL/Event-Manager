@php
    $isEdit = isset($event);
    $isOrganizer = auth()->user()->isOrganizer();
    $isAdmin = auth()->user()->isAdmin();
    $formAction = $isEdit
        ? ($isOrganizer ? route('organizer.events.update', $event) : route('events.update', $event))
        : route('events.store');
    $cancelUrl = $isOrganizer
        ? route('organizer.dashboard')
        : ($isEdit ? route('events.show', $event) : route('events.index'));
    $submitText = $isEdit ? 'Update Festival' : 'Create Festival';
    $submitIcon = $isEdit ? 'M3.75 3v11.25A2.25 2.25 0 016 16.5h2.25M3.75 3h-1.5A2.25 2.25 0 000 5.25v11.25A2.25 2.25 0 012.25 19h11.25A2.25 2.25 0 0116 16.75V14.5m0 0H18m-2.25 0l2.15 2.15a12.06 12.06 0 01-3.63 3.63' : 'M12 4.5v15m7.5-7.5h-15';
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="space-y-6" novalidate>
    @csrf
    @if($isEdit) @method('PUT') @endif

    @if($isAdmin)
        <div class="form-group">
            <label for="organizer_id" class="label">Organizer <span class="text-danger-500" aria-hidden="true">*</span></label>
            <select id="organizer_id"
                    name="organizer_id"
                    required
                    class="input @error('organizer_id') input-error @enderror">
                <option value="">Select an organizer</option>
                @foreach($organizers as $organizer)
                    <option value="{{ $organizer->id }}" {{ (string) old('organizer_id', $isEdit ? $event->organizer_id : '') === (string) $organizer->id ? 'selected' : '' }}>
                        {{ $organizer->name }} ({{ $organizer->email }})
                    </option>
                @endforeach
            </select>
            @error('organizer_id')
                <p class="form-error" role="alert">{{ $message }}</p>
            @enderror
            <p class="form-hint">Only this organizer can manage the event.</p>
        </div>
    @endif

    <!-- Title -->
    <div class="form-group">
        <label for="title" class="label">Title <span class="text-danger-500" aria-hidden="true">*</span></label>
        <input type="text"
               id="title"
               name="title"
               value="{{ old('title', $isEdit ? $event->title : '') }}"
               required
               class="input @error('title') input-error @enderror"
               placeholder="Enter event title"
               aria-describedby="@error('title') title-error @endif @error('title') title-hint @endif"
               autocomplete="off">
        @error('title')
            <p id="title-error" class="form-error" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <!-- Description -->
    <div class="form-group">
        <label for="description" class="label">Description</label>
        <textarea id="description"
                  name="description"
                  rows="5"
                  class="input @error('description') input-error @enderror resize-y"
                  placeholder="Describe your event...">{{ old('description', $isEdit ? $event->description : '') }}</textarea>
        @error('description')
            <p class="form-error" role="alert">{{ $message }}</p>
        @enderror
        <p id="description-hint" class="form-hint">Markdown is supported. Use it to format your description.</p>
    </div>

    <!-- Date & Time -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="form-group">
            <label for="start_date" class="label">Start Date <span class="text-danger-500" aria-hidden="true">*</span></label>
            <input type="datetime-local"
                   id="start_date"
                   name="start_date"
                   value="{{ old('start_date', $isEdit ? $event->start_date->format('Y-m-d\TH:i') : '') }}"
                   required
                   class="input @error('start_date') input-error @enderror"
                   aria-describedby="@error('start_date') start_date-error @endif">
            @error('start_date')
                <p id="start_date-error" class="form-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="end_date" class="label">End Date <span class="text-danger-500" aria-hidden="true">*</span></label>
            <input type="datetime-local"
                   id="end_date"
                   name="end_date"
                   value="{{ old('end_date', $isEdit ? $event->end_date->format('Y-m-d\TH:i') : '') }}"
                   required
                   class="input @error('end_date') input-error @enderror"
                   aria-describedby="@error('end_date') end_date-error @endif">
            @error('end_date')
                <p id="end_date-error" class="form-error" role="alert">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Location -->
    <div class="form-group">
        <label for="location" class="label">Location</label>
        <input type="text"
               id="location"
               name="location"
               value="{{ old('location', $isEdit ? $event->location : '') }}"
               class="input @error('location') input-error @enderror"
               placeholder="e.g., Jakarta Convention Center, Online (Zoom), etc."
               autocomplete="off">
        @error('location')
            <p class="form-error" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <!-- City & Category -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="form-group">
            <label for="city" class="label">City</label>
            <select id="city"
                    name="city"
                    class="input @error('city') input-error @enderror"
                    aria-describedby="city-hint">
                <option value="">Select City</option>
                @foreach(['Jakarta', 'Bali', 'Bandung', 'Surabaya', 'Yogyakarta', 'Medan', 'Makassar', 'Semarang', 'Palembang', 'Other'] as $cityOption)
                    <option value="{{ $cityOption }}" {{ (old('city', $isEdit ? $event->city : '') === $cityOption) ? 'selected' : '' }}>{{ $cityOption }}</option>
                @endforeach
            </select>
            @error('city')
                <p class="form-error" role="alert">{{ $message }}</p>
            @enderror
            <p id="city-hint" class="form-hint">Select the city where the festival takes place.</p>
        </div>

        <div class="form-group">
            <label for="category" class="label">Category</label>
            <select id="category"
                    name="category"
                    class="input @error('category') input-error @enderror"
                    aria-describedby="category-hint">
                <option value="">Select Category</option>
                @foreach([
                    'music' => 'Music Festivals',
                    'electronic' => 'EDM & Electronic',
                    'rock' => 'Rock & Indie',
                    'cultural' => 'Cultural & Arts',
                    'food' => 'Food & Drink',
                    'comedy' => 'Comedy & Shows',
                    'wellness' => 'Wellness & Yoga',
                    'family' => 'Family & Kids',
                ] as $key => $label)
                    <option value="{{ $key }}" {{ (old('category', $isEdit ? $event->category : '') === $key) ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('category')
                <p class="form-error" role="alert">{{ $message }}</p>
            @enderror
            <p id="category-hint" class="form-hint">Choose the festival category for better discovery.</p>
        </div>
    </div>

    <!-- Featured Toggle -->
    <div class="form-group">
        <label class="label flex items-center gap-3 cursor-pointer">
            <input type="checkbox"
                   name="is_featured"
                   id="is_featured"
                   value="1"
                   {{ old('is_featured', $isEdit ? $event->is_featured : false) ? 'checked' : '' }}
                   class="w-5 h-5 rounded border-secondary-300 text-primary-600 focus:ring-primary-500">
            <span class="text-sm font-medium text-secondary-700">Feature this festival</span>
        </label>
        <p class="form-hint">Featured festivals appear prominently on the homepage and in search results.</p>
    </div>

    <!-- Event Image -->
    <div class="form-group">
        <label for="image" class="label">Event Image</label>
        @if($isEdit && $event->image)
            <div class="mb-3">
                <p class="text-sm text-secondary-500 mb-2">Current Image</p>
                <div class="relative inline-block">
                    <img src="{{ asset('storage/' . $event->image) }}"
                         alt="Current event image"
                         class="w-40 h-40 object-cover rounded-lg border border-secondary-200">
                    <label for="remove_image"
                           class="absolute -top-2 -right-2 btn btn-danger btn-icon-sm rounded-full shadow-lg hover:bg-danger-700"
                           aria-label="Remove current image">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </label>
                    <input type="checkbox" id="remove_image" name="remove_image" value="1" class="sr-only">
                </div>
            </div>
        @endif

        <input type="file"
               id="image"
               name="image"
               accept="image/*"
               class="input @error('image') input-error @enderror"
               aria-describedby="@error('image') image-error @endif image-hint">
        @error('image')
            <p id="image-error" class="form-error" role="alert">{{ $message }}</p>
        @enderror
        <p id="image-hint" class="form-hint">
            @if($isEdit && $event->image) Leave empty to keep current image. @endif
            Max 2MB. Supported formats: JPG, PNG, GIF, WebP.
        </p>
    </div>

    <!-- Status & Max Attendees -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="form-group">
            <label for="status" class="label">Status <span class="text-danger-500" aria-hidden="true">*</span></label>
            <select id="status"
                    name="status"
                    required
                    class="input @error('status') input-error @enderror"
                    aria-describedby="@error('status') status-error @endif status-hint">
                <option value="draft" {{ (old('status', $isEdit ? $event->status : 'draft') === 'draft') ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ (old('status', $isEdit ? $event->status : '') === 'published') ? 'selected' : '' }}>Published</option>
                <option value="cancelled" {{ (old('status', $isEdit ? $event->status : '') === 'cancelled') ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')
                <p id="status-error" class="form-error" role="alert">{{ $message }}</p>
            @enderror
            <p id="status-hint" class="form-hint">
                <strong>Draft:</strong> Visible only to admins. <strong>Published:</strong> Visible to everyone. <strong>Cancelled:</strong> Event is cancelled.
            </p>
        </div>

        <div class="form-group">
            <label for="max_attendees" class="label">Max Attendees</label>
            <input type="number"
                   id="max_attendees"
                   name="max_attendees"
                   value="{{ old('max_attendees', $isEdit ? $event->max_attendees : '') }}"
                   min="1"
                   class="input @error('max_attendees') input-error @enderror"
                   placeholder="Leave empty for unlimited"
                   aria-describedby="max_attendees-hint @error('max_attendees') max_attendees-error @endif">
            @error('max_attendees')
                <p id="max_attendees-error" class="form-error" role="alert">{{ $message }}</p>
            @enderror
            <p id="max_attendees-hint" class="form-hint">Leave empty for unlimited attendance.</p>
        </div>
    </div>

    <!-- Submit Actions -->
    <div class="flex flex-col sm:flex-row gap-3 justify-end pt-4 border-t border-secondary-200">
        <a href="{{ $cancelUrl }}" class="btn btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                {{ $submitIcon }}
            </svg>
            {{ $submitText }}
        </button>
    </div>
</form>