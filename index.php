<?include 'function.php';?>
<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
</head>

<body class="p-3">
<main class="container mt-4">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button"
                    role="tab" aria-controls="home" aria-selected="true">Список групп</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button"
                    role="tab" aria-controls="profile" aria-selected="false">Список контактов</button>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row mt-4" id="group-list">
                <div class="col-12 mb-4 d-flex justify-content-between">
                    <div class="justify-content-start">
                        <h3>Группы</h3>
                        <small class="form-text text-muted">Количество групп: <?=count(groupContact());?></small>
                    </div>
                    <div class="justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#add-group">Добавить группу</button>
                    </div>
                </div>
                <?foreach (groupContact() as $item):?>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card text-start" id="click-group" data-id=<?=$item['id']?>">
                        <div class="card-body">
                            <h4 class="card-title"><?=$item['name']?></h4>
                            <p class="card-text">Пациентов: <?=$item['countContact']?></p>
                        </div>
                    </div>
                </div>
                <?endforeach;?>
            </div>
            <div class="row mt-4" id="contact-list">
                <div class="col-12 d-flex justify-content-between">
                    <div class="justify-content-start">
                        <h3 id="name-group"></h3>
                        <small class="form-text text-muted">Пациентов: <span id="count-contact"></span></small>
                    </div>
                    <div class="justify-content-end">
                        <button type="button" class="btn btn-success btn-sm me-4" data-bs-toggle="modal"
                                data-bs-target="#add-user">Добавить пользователя</button>
                        <button type="button" class="btn btn-warning btn-sm" id="show-group">Назад</button>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th scope="col">ФИО</th>
                                <th scope="col">Дата поступления</th>
                                <th scope="col">Срок пребывания</th>
                                <th scope="col">Пол</th>
                                <th scope="col">Возраст(лет)</th>
                                <th scope="col">Действия</th>
                            </tr>
                            </thead>
                            <tbody id="result-table">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-between">
                    <div class="d-flex justify-content-between align-items-end w-100">
                        <div class="me-3 w-100">
                            <small class="form-text text-muted">Группа</small>
                            <select class="form-select" id="filter-user-group">
                                <option value="" selected>не выбрано</option>
                                <? foreach (groupContact() as $item):?>
                                    <option value="<?=$item['id']?>"><?=$item['name']?></option>
                                <?endforeach;?>
                            </select>
                        </div>
                        <div class="me-3 w-100">
                            <small class="form-text text-muted">Пол</small>
                            <select class="form-select" id="filter-male">
                                <option value="" selected>не выбрано</option>
                                <option value="299">Мужской</option>
                                <option value="300">Женский</option>
                            </select>
                        </div>
                        <div class="me-3 w-100">
                            <small class="form-text text-muted">Возраст</small>
                            <input type="number" class="form-control" id="filter-old">
                        </div>
                        <button type="button" class="btn btn-success" id="filter-contact">Применить</button>
                    </div>
                </div>
            </div>
            <div class="row mt-4" id="result-search-contact">

            </div>
        </div>
    </div>
</main>
<div class="modal fade" id="add-group" tabindex="-1" data-bs-keyboard="false" role="dialog"
     aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Новая группа</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <small class="form-text text-muted">Название группы</small>
                    <input type="text" class="form-control" id="name-group-add">
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-success" id="add-group-btn">Добавить</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add-user" tabindex="-1" data-bs-keyboard="false" role="dialog"
     aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Новый пользователь</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between">
                    <button class="btn btn-success btn-sm" id="new-contact">Новый пациент</button>
                    <button class="btn btn-primary btn-sm" id="select-contact">Существующий пациент</button>
                </div>
                <input type="hidden" id="select-contact-id">
                <div class="new-contact">
                    <div class="mb-3">
                        <small class="form-text text-muted">Фамилия</small>
                        <input type="text" class="form-control" id="lastname">
                    </div>
                    <div class="mb-3">
                        <small class="form-text text-muted">Имя</small>
                        <input type="text" class="form-control" id="name">
                    </div>
                    <div class="mb-3">
                        <small class="form-text text-muted">Дата поступления</small>
                        <input type="date" class="form-control" id="date1">
                    </div>
                    <div class="mb-3">
                        <small class="form-text text-muted">Пол</small>
                        <select class="form-select" id="male">
                            <option selected>не выбрано</option>
                            <option value="299">Мужской</option>
                            <option value="300">Женский</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <small class="form-text text-muted">Возраст</small>
                        <input type="text" class="form-control" id="date2">
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-success" id="add-new-contact-btn">Добавить</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="move-user" tabindex="-1" data-bs-keyboard="false" role="dialog"
     aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Переместить пользователя</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <small class="form-text text-muted">Группа</small>
                    <select class="form-select" id="remove-user-group">
                        <option value="" selected>не выбрано</option>
                        <? foreach (groupContact() as $item):?>
                        <option value="<?=$item['id']?>"><?=$item['name']?></option>
                        <?endforeach;?>
                    </select>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-success" id="remove-user-btn">Переместить</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete-user" tabindex="-1" data-bs-keyboard="false" role="dialog"
     aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Удалить пользователя?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="delete-user-modal-btn">Да</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
</script>
<script src="https://kit.fontawesome.com/b675a8d36a.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="//api.bitrix24.com/api/v1/"></script>
<script src="main.js"></script>
</body>

</html>