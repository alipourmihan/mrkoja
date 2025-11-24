<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">افزودن محله</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="ajaxForm" class="modal-form create"
          action="{{ route('admin.listing_specification.location.store_neighborhood') }}" method="post"
          enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label for="">زبان <span class="text-danger">*</span></label>
            <select name="m_language_id" class="form-control">
              <option selected disabled>انتخاب زبان</option>
              @foreach ($langs as $lang)
                <option value="{{ $lang->id }}">{{ $lang->name }}</option>
              @endforeach
            </select>
            <p id="err_m_language_id" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="form-group d-none" id="hide_state">
            <label for="">استان <span class="text-danger">*</span></label>
            <select name="state_id" class="form-control" id="create_state_id">
              <option selected disabled>انتخاب استان</option>
            </select>
            <p id="err_state_id" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="form-group d-none" id="hide_city">
            <label for="">شهر <span class="text-danger">*</span></label>
            <select name="city_id" class="form-control" id="create_city_id">
              <option selected disabled>انتخاب شهر</option>
            </select>
            <p id="err_city_id" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">نام محله <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="نام محله را وارد کنید">
            <p id="err_name" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">Slug (URL)</label>
            <input type="text" class="form-control ltr" name="slug" placeholder="مثال: tyatrshhr">
            <small class="text-muted">اگر خالی بگذارید، از نام ساخته می‌شود</small>
            <p id="err_slug" class="mt-2 mb-0 text-danger em"></p>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
          بستن
        </button>
        <button id="submitBtn" type="button" class="btn btn-primary btn-sm">
          ذخیره
        </button>
      </div>
    </div>
  </div>
</div>

