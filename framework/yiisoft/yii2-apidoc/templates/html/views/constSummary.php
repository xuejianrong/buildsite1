<?php

use yii\apidoc\helpers\ApiMarkdown;
use yii\apidoc\models\ClassDoc;
use yii\helpers\ArrayHelper;

/* @var $type ClassDoc */
/* @var $this yii\web\View */
/* @var $renderer \yii\apidoc\templates\html\ApiRenderer */

$renderer = $this->context;

if (empty($type->constants)) {
    return;
}
$constants = $type->constants;
ArrayHelper::multisort($constants, 'name');
?>
<div class="summary doc-const">
    <h2>常量</h2>

    <p><a href="#" class="toggle">隐藏从父类继承的常量</a></p>

    <table class="summary-table table table-striped table-bordered table-hover">
    <colgroup>
        <col class="col-const" />
        <col class="col-value" />
        <col class="col-description" />
        <col class="col-defined" />
    </colgroup>
    <tr>
        <th>常量名</th><th>值</th><th>说明</th><th>在哪个类声明的</th>
    </tr>
    <?php foreach ($constants as $constant): ?>
        <tr<?= $constant->definedBy != $type->name ? ' class="inherited"' : '' ?> id="<?= $constant->name ?>">
          <td><?= $constant->name ?><a name="<?= $constant->name ?>-detail"></a></td>
          <td><?= $constant->value ?></td>
          <td><?= ApiMarkdown::process($constant->shortDescription . "\n" . $constant->description, $constant->definedBy, true) ?></td>
          <td><?= $renderer->createTypeLink($constant->definedBy) ?></td>
        </tr>
    <?php endforeach; ?>
    </table>
</div>
