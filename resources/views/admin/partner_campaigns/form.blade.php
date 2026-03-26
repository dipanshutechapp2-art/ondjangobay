<div class="form-group">
    <label for="name">Campaign Name</label>
    <input type="text" name="name" id="name" class="form-control" 
        value="{{ old('name', $partnerCampaign->name ?? '') }}" required>
</div>

{{-- <div class="form-group">
    <label for="frequency">Frequency</label>
    <select name="frequency" id="frequency" class="form-control" required>
        <option value="">-- Select --</option>
        <option value="weekly" {{ old('frequency', $partnerCampaign->frequency ?? '')=='weekly'?'selected':'' }}>Weekly</option>
        <option value="biweekly" {{ old('frequency', $partnerCampaign->frequency ?? '')=='biweekly'?'selected':'' }}>Biweekly</option>
        <option value="monthly" {{ old('frequency', $partnerCampaign->frequency ?? '')=='monthly'?'selected':'' }}>Monthly</option>
    </select>
</div> --}}

<div class="form-group">
    <label for="start_date">Start Date</label>
    <input type="date" name="start_date" class="form-control" 
        value="{{ old('start_date', isset($partnerCampaign) ? \Illuminate\Support\Carbon::parse($partnerCampaign->start_date)->format('Y-m-d') : '') }}" required>
</div>

<div class="form-group">
    <label for="end_date">End Date</label>
    <input type="date" name="end_date" class="form-control" 
        value="{{ old('end_date', isset($partnerCampaign) ? \Illuminate\Support\Carbon::parse($partnerCampaign->end_date)->format('Y-m-d') : '') }}" required>
</div>

<div class="form-group">
    <label for="upload_deadline">Upload Deadline</label>
    <input type="date" name="upload_deadline" class="form-control" 
        value="{{ old('upload_deadline', isset($partnerCampaign) && $partnerCampaign->upload_deadline ? \Illuminate\Support\Carbon::parse($partnerCampaign->upload_deadline)->format('Y-m-d') : '') }}">
</div>


<div class="form-group">
    <label for="min_value">Minimum Value</label>
    <input type="number" step="0.01" name="min_value" class="form-control" 
        value="{{ old('min_value', $partnerCampaign->min_value ?? '') }}">
</div>

<div class="form-group">
    <label for="min_quantity">Minimum Quantity</label>
    <input type="number" name="min_quantity" class="form-control" 
        value="{{ old('min_quantity', $partnerCampaign->min_quantity ?? '') }}" required>
</div>

{{-- <div class="form-group">
    <label for="goal_quantity">Goal Quantity</label>
    <input type="number" name="goal_quantity" class="form-control" 
        value="{{ old('goal_quantity', $partnerCampaign->goal_quantity ?? '') }}">
</div> --}}

<div class="form-group">
    <label for="category_id">Category</label>
    <select name="category_id" class="form-control">
        <option value="">-- Select Category --</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $partnerCampaign->category_id ?? '')==$category->id ? 'selected':'' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- <div class="form-group">
    <label for="cart_timer_minutes">Cart Timer (minutes)</label>
    <input type="number" name="cart_timer_minutes" class="form-control" 
        value="{{ old('cart_timer_minutes', $partnerCampaign->cart_timer_minutes ?? '') }}">
</div> --}}

<div class="form-group">
    <label for="cart_max_volume">Cart Max Volume</label>
    <input type="number" name="cart_max_volume" class="form-control" 
        value="{{ old('cart_max_volume', $partnerCampaign->cart_max_volume ?? '') }}">
</div>

<div class="form-group">
    <label>Status</label>
    <select name="status" class="form-control" required>
        <option value="draft" {{ (old('status', $partnerCampaign->status ?? '')=='draft')?'selected':'' }}>Draft</option>
        <option value="active" {{ (old('status', $partnerCampaign->status ?? '')=='active')?'selected':'' }}>Active</option>
        <option value="closed" {{ (old('status', $partnerCampaign->status ?? '')=='closed')?'selected':'' }}>Closed</option>
    </select>
</div>
