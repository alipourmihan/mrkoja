<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">ویرایش محله</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="ajaxEditForm" class="modal-form {{ $language->direction == 1 ? 'rtl text-left' : '' }}"
          action="{{ route('admin.listing_specification.location.update_neighborhood') }}" method="post">
          @csrf
          <input type="hidden" name="id" id="in_id">

          <div class="form-group d-none" id="e_hide_state">
            <label for="">استان <span class="text-danger">*</span></label>
            <select name="state_id" class="form-control state_id" id="in_state_id">
              <option selected disabled>انتخاب استان</option>
              @foreach ($states as $state)
                <option value="{{ $state->id }}">{{ $state->name }}</option>
              @endforeach
            </select>
            <p id="editErr_state_id" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="form-group d-none" id="e_hide_city">
            <label for="">شهر <span class="text-danger">*</span></label>
            <select name="city_id" class="form-control city_id" id="in_city_id">
              <option selected disabled>انتخاب شهر</option>
            </select>
            <p id="editErr_city_id" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">نام محله <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="نام محله را وارد کنید" id="in_name">
            <p id="editErr_name" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">Slug (URL)</label>
            <input type="text" class="form-control ltr" name="slug" placeholder="مثال: tyatrshhr" id="in_slug">
            <small class="text-muted">اگر خالی بگذارید، از نام ساخته می‌شود</small>
            <p id="editErr_slug" class="mt-2 mb-0 text-danger em"></p>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
          بستن
        </button>
        <button id="updateBtn" type="button" class="btn btn-primary btn-sm">
          به‌روزرسانی
        </button>
      </div>
    </div>
  </div>
</div>

