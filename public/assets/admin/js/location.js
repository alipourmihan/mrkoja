"use strict";
$(document).ready(function () {
  // Auto-generate slug from name
  function createSlug(text) {
    return text
      .toString()
      .toLowerCase()
      .replace(/\s+/g, '-')
      .replace(/[^\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFFa-z0-9-]+/g, '')
      .replace(/--+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '');
  }

  // Auto-generate slug for create form (State and City)
  $('input[name="name"]').on('blur', function() {
    var nameInput = $(this);
    var slugInput = $('input[name="slug"]');
    if (!slugInput.data('manually-changed') && (!slugInput.val() || slugInput.data('auto-generated'))) {
      var slug = createSlug(nameInput.val());
      slugInput.val(slug);
      slugInput.data('auto-generated', true);
    }
  });

  // Track manual slug changes
  $('input[name="slug"]').on('input', function() {
    $(this).data('manually-changed', true);
    $(this).data('auto-generated', false);
  });

  // Handle edit button click for State and City
  $('.editBtn').on('click', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var slug = $(this).data('slug') || '';
    var countryId = $(this).data('country_id');
    var stateId = $(this).data('state_id');
    
    $('#in_id').val(id);
    $('#in_name').val(name);
    if ($('#in_slug').length) {
      $('#in_slug').val(slug);
    }
    if (countryId && $('#in_country_id').length) {
      $('#in_country_id').val(countryId);
    }
    if (stateId && $('#in_state_id').length) {
      $('#in_state_id').val(stateId);
    }
  });

  $('select[name="m_language_id"]').on('change', function () {
    var selectedLanguageId = $(this).val();

    var createStateSelect = $('#hide_state select[name="state_id"]');
    var editStateSelect = $('#e_hide_state select[name="state_id"]');

    createStateSelect.empty();
    editStateSelect.empty();

    if (selectedLanguageId) {
      $.ajax({
        url: baseUrl + 'admin/listing-specification/location/get-country/' + selectedLanguageId,
        type: 'GET',
        data: { language_id: selectedLanguageId },
        success: function (response) {
          if (response.states && response.states.length > 0) {
            $('#hide_state').removeClass('d-none');
            $('#e_hide_state').removeClass('d-none');

            var placeholderOption = $('<option>', {
              value: '',
              text: 'Select a state',
              disabled: true,
              selected: true
            });

            createStateSelect.append(placeholderOption.clone());
            editStateSelect.append(placeholderOption.clone());

              $.each(response.states, function (index, state) {
              var option = $('<option>', {
                  value: state.id,
                  text: state.name
              });
              createStateSelect.append(option.clone());
              editStateSelect.append(option.clone());
              });
            } else {
              $('#hide_state').addClass('d-none');
            $('#e_hide_state').addClass('d-none');
          }
        },
        error: function () {
          console.error('Error fetching States');
        }
              });
            } else {
      $('#hide_state').addClass('d-none');
              $('#e_hide_state').addClass('d-none');
    }
  });
});
