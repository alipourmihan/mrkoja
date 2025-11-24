"use strict";
(function() {
  function initNeighborhood() {
    if (typeof window.jQuery !== 'undefined') {
      window.jQuery(document).ready(function($) {
        // Language change handler
        $('select[name="m_language_id"]').on('change', function () {
          var selectedLanguageId = $(this).val();

          var createStateSelect = $('#hide_state select[name="state_id"]');
          var editStateSelect = $('#e_hide_state select[name="state_id"]');
          var createCitySelect = $('#hide_city select[name="city_id"]');
          var editCitySelect = $('#e_hide_city select[name="city_id"]');

          createStateSelect.empty();
          editStateSelect.empty();
          createCitySelect.empty();
          editCitySelect.empty();

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
                    text: 'انتخاب استان',
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
                  $('#hide_city').addClass('d-none');
                  $('#e_hide_city').addClass('d-none');
                }
              },
              error: function () {
                console.error('Error fetching States');
              }
            });
          } else {
            $('#hide_state').addClass('d-none');
            $('#e_hide_state').addClass('d-none');
            $('#hide_city').addClass('d-none');
            $('#e_hide_city').addClass('d-none');
          }
        });

        // State change handler for create form
        $('#create_state_id').on('change', function () {
          var stateId = $(this).val();
          var citySelect = $('#create_city_id');
          
          citySelect.empty();
          citySelect.append($('<option>', {
            value: '',
            text: 'انتخاب شهر',
            disabled: true,
            selected: true
          }));

          if (stateId) {
            $.ajax({
              url: baseUrl + 'admin/listing-specification/location/get-city-neighborhood',
              type: 'POST',
              data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: stateId
              },
              success: function (response) {
                if (response.status === 'success' && response.cities && response.cities.length > 0) {
                  $('#hide_city').removeClass('d-none');
                  
                  $.each(response.cities, function (index, city) {
                    var option = $('<option>', {
                      value: city.id,
                      text: city.name
                    });
                    citySelect.append(option);
                  });
                } else {
                  $('#hide_city').addClass('d-none');
                }
              },
              error: function () {
                console.error('Error fetching Cities');
                $('#hide_city').addClass('d-none');
              }
            });
          } else {
            $('#hide_city').addClass('d-none');
          }
        });

        // State change handler for edit form
        $('#in_state_id').on('change', function () {
          var stateId = $(this).val();
          var citySelect = $('#in_city_id');
          
          citySelect.empty();
          citySelect.append($('<option>', {
            value: '',
            text: 'انتخاب شهر',
            disabled: true,
            selected: true
          }));

          if (stateId) {
            $.ajax({
              url: baseUrl + 'admin/listing-specification/location/get-city-neighborhood',
              type: 'POST',
              data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: stateId
              },
              success: function (response) {
                if (response.status === 'success' && response.cities && response.cities.length > 0) {
                  $('#e_hide_city').removeClass('d-none');
                  
                  $.each(response.cities, function (index, city) {
                    var option = $('<option>', {
                      value: city.id,
                      text: city.name
                    });
                    citySelect.append(option);
                  });
                } else {
                  $('#e_hide_city').addClass('d-none');
                }
              },
              error: function () {
                console.error('Error fetching Cities');
                $('#e_hide_city').addClass('d-none');
              }
            });
          } else {
            $('#e_hide_city').addClass('d-none');
          }
        });

        // Edit button handler
        $('.editBtn').on('click', function() {
          var id = $(this).data('id');
          var stateId = $(this).data('state_id');
          var cityId = $(this).data('city_id');
          var name = $(this).data('name');
          var slug = $(this).data('slug') || '';

          $('#in_id').val(id);
          $('#in_name').val(name);
          $('#in_slug').val(slug);
          $('#in_state_id').val(stateId);

          // Load cities for selected state
          if (stateId) {
            $.ajax({
              url: baseUrl + 'admin/listing-specification/location/get-city-neighborhood',
              type: 'POST',
              data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: stateId
              },
              success: function (response) {
                if (response.status === 'success' && response.cities && response.cities.length > 0) {
                  $('#e_hide_city').removeClass('d-none');
                  
                  var citySelect = $('#in_city_id');
                  citySelect.empty();
                  citySelect.append($('<option>', {
                    value: '',
                    text: 'انتخاب شهر',
                    disabled: true
                  }));
                  
                  $.each(response.cities, function (index, city) {
                    var option = $('<option>', {
                      value: city.id,
                      text: city.name,
                      selected: city.id == cityId
                    });
                    citySelect.append(option);
                  });
                }
              },
              error: function () {
                console.error('Error fetching Cities');
              }
            });
          }

          $('#e_hide_state').removeClass('d-none');
        });

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

        // Auto-generate slug for create form
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
      });
    } else {
      setTimeout(initNeighborhood, 50);
    }
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNeighborhood);
  } else {
    initNeighborhood();
  }
})();

