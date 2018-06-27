<?php

use yii\apidoc\helpers\ApiMarkdown;
use yii\apidoc\models\ClassDoc;
use yii\apidoc\models\InterfaceDoc;
use yii\apidoc\models\TraitDoc;
use yii\helpers\ArrayHelper;

/* @var $type ClassDoc|InterfaceDoc|TraitDoc */
/* @var $protected boolean */
/* @var $this yii\web\View */
/* @var $renderer \yii\apidoc\templates\html\ApiRenderer */

$renderer = $this->context;

if ($protected && count($type->getProtectedMethods()) == 0 || !$protected && count($type->getPublicMethods()) == 0) {
    return;
} ?>

<div class="summary doc-method">
<h2><?= $protected ? '私有方法' : '公共方法' ?></h2>

<p><a href="#" class="toggle">隐藏从父类继承的方法</a></p>

<table class="summary-table table table-striped table-bordered table-hover">
<colgroup>
    <col class="col-method" />
    <col class="col-description" />
    <col class="col-defined" />
</colgroup>
<tr>
  <th>方法名</th><th>作者</th><th>说明</th><th>在哪个类声明的</th><th>单元测试</th>
</tr>
<?php
$methods = $type->methods;
ArrayHelper::multisort($methods, 'name');
foreach ($methods as $method): ?>
    <?php
	$aAuthorList = [];
	$isHasTester = false;
	$testPath = '';
	foreach($method->tags as $oTags){
		if($oTags->getName() == 'author'){
			$aAuthorList[] = $oTags->getContent();
		}
		if($oTags->getName() == 'test'){
			$isHasTester = true;
			$testPath = $oTags->getContent();
		}
	}

	if ($protected && $method->visibility == 'protected' || !$protected && $method->visibility != 'protected'): ?>
    <tr<?= $method->definedBy != $type->name ? ' class="inherited"' : '' ?> id="<?= $method->name ?>()">
        <td><?= $renderer->createSubjectLink($method, $method->name.'()') ?></td>
        <td><?= ApiMarkdown::process(implode('、', $aAuthorList), $method->definedBy, true) ?></td>
        <td class="methodDesc"><?= ApiMarkdown::process($method->shortDescription, $method->definedBy, true) ?></td>
        <td><?= $renderer->createTypeLink($method->definedBy, $type) ?></td>
        <td title="<?= $testPath ?>"><?= $isHasTester ? '有' : '<span style="color:red">无</span>' ?></td>
    </tr>
    <?php endif; ?>
<?php endforeach; ?>
</table>
</div>
