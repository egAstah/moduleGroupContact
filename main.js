$(document).ready(function () {
    $(document).on('click', '#click-group', function () {
        let id = $(this).attr('data-id')
        $('#group-list').hide()
        $('#contact-list').hide()
        $('#result-table').html('')
        $('#add-new-contact-btn').attr('data-group', id)
        $.ajax({
            url: './function.php',
            method: 'post',
            dataType: 'html',
            data: {id: id, 'event': 'print-group'},
            success: function (data) {
                data = JSON.parse(data)
                $('#result-table').html(data.html)
                $('#name-group').text(data.nameGroup)
                $('#count-contact').text(data.countContact)
                $('#contact-list').fadeIn()
            }
        });
    })
    $(document).on('click', '#show-group', function () {
        $('#contact-list').hide()
        $('#group-list').fadeIn()
    })
    $(document).on('click', '#add-group-btn', function () {
        if ($('#name-group-add').val() != '') {
            $.ajax({
                url: './function.php',
                method: 'post',
                dataType: 'html',
                data: {name: $('#name-group-add').val(), 'event': 'add-group'},
                success: function (data) {
                    location.reload()
                }
            });
        } else {
            $('#name-group-add').css('border-color', 'red')
        }
    })
    $(document).on('click', '#open-user', function () {
        BX24.openPath('/crm/contact/details/' + $(this).attr('data-id') + '/');
    })
    $(document).on('click', '#move-user-btn', function () {
        let group = $(this).attr('data-group')
        let user = $(this).attr('data-user')
        $('#remove-user-btn').attr('data-group', group)
        $('#remove-user-btn').attr('data-user', user)
    })
    $(document).on('click', '#remove-user-btn', function () {
        let group = $(this).attr('data-group')
        let user = $(this).attr('data-user')
        let newGroup = $('#remove-user-group').val()
        if ($('#remove-user-group').val() != '') {
            $.ajax({
                url: './function.php',
                method: 'post',
                dataType: 'html',
                data: {
                    group: group,
                    user: user,
                    newGroup: newGroup,
                    'event': 'remove-user'
                },
                success: function (data) {
                    location.reload()
                }
            });
        } else {
            $('#remove-user-group').css('border-color', 'red')
        }
    })
    $(document).on('click', '#delete-user-btn', function () {
        let group = $(this).attr('data-group')
        let user = $(this).attr('data-user')
        $('#delete-user-modal-btn').attr('data-group', group)
        $('#delete-user-modal-btn').attr('data-user', user)
    })
    $(document).on('click', '#delete-user-modal-btn', function () {
        let group = $(this).attr('data-group')
        let user = $(this).attr('data-user')
        $.ajax({
            url: './function.php',
            method: 'post',
            dataType: 'html',
            data: {
                group: group,
                user: user,
                'event': 'delete-user'
            },
            success: function (data) {
                location.reload()
            }
        });
    })
    $(document).on('click', '#filter-contact', function () {
        let group = $('#filter-user-group').val()
        let male = $('#filter-male').val()
        let age = $('#filter-old').val()
        if (group != '' || male != '' || age != '') {
            $.ajax({
                url: './function.php',
                method: 'post',
                dataType: 'html',
                data: {
                    age: age,
                    male: male,
                    group: group,
                    'event': 'selected-contact'
                },
                success: function (data) {
                    // console.log(data)
                    $('#result-search-contact').html(data)
                }
            });
        } else {
            alert('Заполните хотя бы одно поле!')
        }
    })
    $(document).on('click', '#new-contact', function () {
        $('.new-contact').fadeIn()
    })
    $(document).on('click', '#select-contact', function () {
        BX24.selectCRM({
            entityType: ['contact'],
            multiple: false,
        }, function (res) {
            $.ajax({
                url: './function.php',
                method: 'post',
                dataType: 'html',
                data: {
                    arr: res.contact,
                    'event': 'add-selected-contact'
                },
                success: function (data) {
                    $('#select-contact-id').val(data)
                }
            });
        })
    })
    $(document).on('click', '#new-contact', function () {
        $('#select-contact-id').val('')
    })
    $(document).on('click', '#add-new-contact-btn', function () {
        let group = $(this).attr('data-group')
        let dataAjax
        if ($('#select-contact-id').val() != '') {
            dataAjax = {
                id: $('#select-contact-id').val(),
                group: group,
                event: 'add-contact-group'
            }
        } else {
            dataAjax = {
                lastname: $('#lastname').val(),
                name: $('#name').val(),
                date1: $('#date1').val(),
                date2: $('#date2').val(),
                male: $('#male').val(),
                group: group,
                event: 'add-contact-group'
            }
        }
        $.ajax({
            url: './function.php',
            method: 'post',
            dataType: 'html',
            data: dataAjax,
            success: function (data) {
                location.reload()
            }
        });
    })
})