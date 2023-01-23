<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
CModule::IncludeModule("crm");

function groupContact()
{
    $arSelect = array("*", 'PROPERTY_*');
    $arFilter = array("IBLOCK_ID" => 38, "ACTIVE" => "Y");
    $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $result = [];
    while ($ob = $iblock->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        if ($arProps[147]['VALUE'] == '') $count = 0; else $count = count($arProps[147]['VALUE']);
        $result[] = [
            'id' => $arFields['ID'],
            'name' => $arFields['NAME'],
            'countContact' => $count
        ];
    }
    return $result;
}

function searchForId($id, $array)
{
    foreach ($array as $key => $val) {
        if ($val['uid'] === $id) {
            return $key;
        }
    }
    return null;
}

switch ($_POST['event']) {
    case 'print-group':
        $arSelect = array("*", 'PROPERTY_*');
        $arFilter = array("IBLOCK_ID" => 38, "ACTIVE" => "Y", 'ID' => $_POST['id']);
        $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $result = [];
        $nameGroup = '';
        while ($ob = $iblock->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();
            $nameGroup = $arFields['NAME'];
            $result = $arProps[147]['VALUE'];
        }
        $resultContact = [];
        foreach ($result as $item) {
            $arFilter = array('ID' => $item, 'CHECK_PERMISSIONS' => 'N');
            $arSelect = ['NAME', 'LAST_NAME', 'SECOND_NAME', 'UF_CRM_1667390235062', 'UF_CRM_1666366366545', 'UF_CRM_1667319789179'];
            $rsContact = CCrmContact::GetList(array(), $arFilter, $arSelect);
            if ($arCont = $rsContact->Fetch()) {
                $date1 = $arCont['UF_CRM_1667390235062'];
                $date2 = date('d.m.Y');
                $diff = abs(strtotime($date2) - strtotime($date1));
                $years = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                if ($date1 == '') $days = '';

                $male = $arCont['UF_CRM_1666366366545'];
                if ($male == 299) $male = 'Мужской'; else if ($male == 300) $male = 'Женский'; else $male = '';

                $old = $arCont['UF_CRM_1667319789179'];
                $resultContact[] = [
                    'id' => $arCont['ID'],
                    'name' => $arCont['NAME'] . ' ' . $arCont['LAST_NAME'],
                    'datePustupleniya' => $date1,
                    'dayPrebivaniya' => $days,
                    'male' => $male,
                    'old' => $old
                ];
            }
        }
        $html = '';
        foreach ($resultContact as $item) {
            $html .= '
            <tr>
                <td>' . $item['name'] . '</td>
                <td>' . $item['datePustupleniya'] . '</td>
                <td>' . $item['dayPrebivaniya'] . '</td>
                <td>' . $item['male'] . '</td>
                <td>' . $item['old'] . '</td>
                <td>
                    <button class="btn-action" id="open-user" data-id="' . $item['id'] . '"><i class="fa-solid fa-link"></i></button>
                    <button class="btn-action" id="move-user-btn" data-group="' . $_POST['id'] . '" data-user="' . $item['id'] . '" data-bs-toggle="modal" data-bs-target="#move-user"><i class="fa-solid fa-up-down-left-right"></i></button>
                    <button class="btn-action" id="delete-user-btn" data-bs-toggle="modal" data-group="' . $_POST['id'] . '" data-user="' . $item['id'] . '" data-bs-target="#delete-user"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
            ';
        }
        $resultData = [
            'html' => $html,
            'countContact' => count($resultContact),
            'nameGroup' => $nameGroup
        ];
        echo json_encode($resultData);
        break;
    case 'add-group':
        $el = new CIBlockElement;
        $PROP = [];
        $arFields = array(
            'IBLOCK_ID' => 38,
            'PROPERTY_VALUES' => $PROP,
            'NAME' => $_POST['name'],
        );
        $el->Add($arFields);
        break;
    case 'remove-user':
        $arSelect = array("*", 'PROPERTY_*');
        $arFilter = array("IBLOCK_ID" => 38, "ACTIVE" => "Y", 'ID' => $_POST['group']);
        $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $result = [];
        while ($ob = $iblock->GetNextElement()) {
            $arProps = $ob->GetProperties();
            $result = $arProps[147]['VALUE'];
        }
        $keyRemoveUser = array_search($_POST['user'], $result);
        unset($result[$keyRemoveUser]);
        $result = array_values($result);

        $el = new CIBlockElement;
        $PROP = [];
        $PROP[147] = $result;
        $arUpdate = array(
            "MODIFIED_BY" => 1,
            "PROPERTY_VALUES" => $PROP
        );
        $el->Update($_POST['group'], $arUpdate);


        $arSelect = array("*", 'PROPERTY_*');
        $arFilter = array("IBLOCK_ID" => 38, "ACTIVE" => "Y", 'ID' => $_POST['newGroup']);
        $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $resultNewGroup = [];
        while ($ob = $iblock->GetNextElement()) {
            $arProps = $ob->GetProperties();
            $resultNewGroup = $arProps[147]['VALUE'];
        }
        $resultNewGroup[] = $_POST['user'];
        $PROP[147] = $resultNewGroup;
        $arUpdate = array(
            "MODIFIED_BY" => 1,
            "PROPERTY_VALUES" => $PROP
        );
        $el->Update($_POST['newGroup'], $arUpdate);
        break;
    case 'delete-user':
        $arSelect = array("*", 'PROPERTY_*');
        $arFilter = array("IBLOCK_ID" => 38, "ACTIVE" => "Y", 'ID' => $_POST['group']);
        $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $result = [];
        while ($ob = $iblock->GetNextElement()) {
            $arProps = $ob->GetProperties();
            $result = $arProps[147]['VALUE'];
        }

        $keyRemoveUser = array_search($_POST['user'], $result);
        unset($result[$keyRemoveUser]);
        $result = array_values($result);

        $el = new CIBlockElement;
        $PROP = [];
        $PROP[147] = $result;
        $arUpdate = array(
            "MODIFIED_BY" => 1,
            "PROPERTY_VALUES" => $PROP
        );
        $el->Update($_POST['group'], $arUpdate);

        break;
    case 'selected-contact':
        $arr1 = [];
        $arr2 = [];
        $arIdContact = [];
        if($_POST['group']){
            $arSelect = array("*", 'PROPERTY_*');
            $arFilter = array("IBLOCK_ID" => 38, "ACTIVE" => "Y", 'ID' => $_POST['group']);
            $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            $result = [];
            $nameGroup = '';
            while ($ob = $iblock->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arProps = $ob->GetProperties();
                $nameGroup = $arFields['NAME'];
                $arIdContact = $arProps[147]['VALUE'];
            }
        }
        if($_POST['male'] != '') $arr1 = ['UF_CRM_1666366366545' => $_POST['male']];
        if($_POST['age'] != '') $arr2 = ['UF_CRM_1667319789179' => $_POST['age']];
        $arFilterContact = array_merge($arr1, $arr2, ['ID' => $arIdContact, 'CHECK_PERMISSIONS' => 'N']);
        foreach ($arIdContact as $item) {
            $arFilterContact = array_merge($arFilterContact, ['ID' => $item, 'CHECK_PERMISSIONS' => 'N']);
            $arSelect = ['NAME', 'LAST_NAME', 'SECOND_NAME', 'UF_CRM_1667390235062', 'UF_CRM_1666366366545', 'UF_CRM_1667319789179'];
            $rsContact = CCrmContact::GetList(array(), $arFilterContact, $arSelect);
            if ($arCont = $rsContact->Fetch()) {
                $date1 = $arCont['UF_CRM_1667390235062'];
                $date2 = date('d.m.Y');
                $diff = abs(strtotime($date2) - strtotime($date1));
                $years = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                if ($date1 == '') $days = '';

                $male = $arCont['UF_CRM_1666366366545'];
                if ($male == 299) $male = 'Мужской'; else if ($male == 300) $male = 'Женский'; else $male = '';

                $old = $arCont['UF_CRM_1667319789179'];
                $resultContact[] = [
                    'id' => $arCont['ID'],
                    'name' => $arCont['NAME'] . ' ' . $arCont['LAST_NAME'],
                    'datePustupleniya' => $date1,
                    'dayPrebivaniya' => $days,
                    'male' => $male,
                    'old' => $old
                ];
            }
        }
        $html = '';
        foreach ($resultContact as $item) {
            $html .= '
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card text-start">
                        <div class="card-body">
                            <h4 class="card-title">' . $item['name'] . '</h4>
                            <p class="card-text">
                            <ul class="list-unstyled">
                                <li>
                                    <a href="#" id="open-user" data-id="' . $item['id'] . '" aria-current="page">Ссылка</a>
                                </li>
                                <li>
                                    <span>Дата поступления: ' . $item['datePustupleniya'] . '</span>
                                </li>
                                <li>
                                    <span>Срок пребывания: ' . $item['dayPrebivaniya'] . '</span>
                                </li>
                                <li>
                                    <span>Пол: ' . $item['male'] . '</span>
                                </li>
                                <li>
                                    <span>Возраст: ' . $item['old'] . '</span>
                                </li>
                                <li>
                                    <span>Группа: ' . $nameGroup . '</span>
                                </li>
                            </ul>
                            </p>
                        </div>
                    </div>
                </div>
            ';
        }
        if(count($resultContact) > 0) echo $html;
        else echo '<h3>Пациентов не найдено</h3>';
        break;
    case 'add-selected-contact':
        $id = explode('_', $_POST['arr'][0]['id'])[1];
        print_r($id);
        break;
    case 'add-contact-group':
        $result = [];
        $arSelect = array("*", 'PROPERTY_*');
        $arFilter = array("IBLOCK_ID" => 38, "ACTIVE" => "Y", 'ID' => $_POST['group']);
        $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        while ($ob = $iblock->GetNextElement()) {
            $arProps = $ob->GetProperties();
            $result = $arProps[147]['VALUE'];
        }
        if($_POST['id'] != ''){
            $result[] = $_POST['id'];
        }else{
            $arFields = array(
                'NAME' => $_POST['name'],
                'LAST_NAME' => $_POST['lastname'],
                'UF_CRM_1667390235062' => date('d.m.Y', strtotime($_POST['date1'])),
                'UF_CRM_1666366366545' => $_POST['male'],
                'UF_CRM_1667319789179' => $_POST['date2']
            );
            $oContact = new \CCrmContact(false);
            $id = $oContact->add($arFields);
            if($oContact->LAST_ERROR == ""){
                $result[] = $id;
            }
        }

        $el = new CIBlockElement;
        $PROP = [];
        $PROP[147] = $result;
        $arUpdate = array(
            "MODIFIED_BY" => 1,
            "PROPERTY_VALUES" => $PROP
        );
        $el->Update($_POST['group'], $arUpdate);
        break;
}