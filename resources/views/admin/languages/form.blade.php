  <form class="geniusform" action="{{ isset($language) ? route('languages.update', $language->id) : route('languages.store') }}" method="POST" enctype="multipart/form-data">
	 @csrf 
		@if(isset($language))
			@method('PUT')
		@endif	 
		<div class="form-group">
			<label for="inp-name">Name</label>
			<input type="text" name="name"class="form-control" id="inp-name" placeholder="Name" value="{{ old('name', $language->name ?? '') }}" required>
			@error('name')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>
		<div class="form-group">
			<label for="inp-email">Code</label>
			<input type="text" name="code"  class="form-control" id="inp-code" value="{{ old('code', $language->code ?? '') }}" placeholder="Enter Email"  required>
			@error('code')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>
		<div class="form-group" >
			<label for="exampleInputEmail1">{{ __('Upload image') }} <span style="color:red;">*</span> </label>
			<input id="select_filess" type="file" class="form-control @error('image') is-invalid @enderror" name="image" placeholder="Select file.." @if(empty($language)) required @endif >
			@if(isset($language) && $language->image)
				<img src="{{ asset('uploads/languages/' . $language->image) }}" alt="Language Image" width="80">
			@endif
			@error('image')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>
		<div class="form-group">
			<input type="checkbox" name="is_default" {{ old('is_default', $language->is_default ?? false) ? 'checked' : '' }}>
            Set as Default
		</div>

		<button type="submit" id="submit-btn" class="btn btn-primary  ">{{ isset($language) ? 'Update' : 'Add' }}</button>

	</form>
