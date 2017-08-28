<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use backend\assets\AppAsset;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="page-header-fixed  page-sidebar-fixed  <?= (isset($_COOKIE['sidebar_toggler']) && $_COOKIE['sidebar_toggler']==1)?'':'page-sidebar-closed';?> ">
    <?php $this->beginBody() ?>

    <?php 
    backend\components\AppComponent::navbarMenu();
    backend\components\AppComponent::navbarRightMenu();
        
    NavBar::begin([
	    'id'=>'main-nav-app',
	    'brandLabel' => isset(Yii::$app->params['company_name'])?Yii::$app->params['company_name']:'My Company',
	    'brandUrl' => Yii::$app->homeUrl,
	    'innerContainerOptions' => ['class'=>'container-fluid'],
	    'options' => [
		'class' => 'page-container navbar navbar-inverse navbar-fixed-top',
	    ],
	]);
	echo Nav::widget([
	    'options' => ['class' => 'navbar-nav'],
	    'items' => isset(Yii::$app->params['navbar'])?Yii::$app->params['navbar']:[],
	]);
	
	echo Nav::widget([
	    'options' => ['class' => 'navbar-nav navbar-right'],
	    'items' => isset(Yii::$app->params['navbarR'])?Yii::$app->params['navbarR']:[],
	]);
	
	echo '<div class="navbar-text pull-right">';
	echo \lajax\languagepicker\widgets\LanguagePicker::widget([
	    'skin' => \lajax\languagepicker\widgets\LanguagePicker::SKIN_DROPDOWN,
	    'size' => \lajax\languagepicker\widgets\LanguagePicker::SIZE_SMALL
	]);
	echo '</div>';
	
	NavBar::end();
    ?>
    
    <?= $this->render('//layouts/_page-sidebar') ?>
		
    <section class="page-container page-content" role="main">
	<div class="sdbox">
	    <div class="page-column">
		<?= Breadcrumbs::widget([
		    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
		]) ?>

		<?php foreach (Yii::$app->session->getAllFlashes() as $message): ?>
		    <?php
			if(isset($message['body'])){
			    echo \yii\bootstrap\Alert::widget([
				'body'=>$message['body'],
				'options'=>$message['options'],
			    ]);
			}
		    ?>
		<?php endforeach; ?>

		<?= $content ?>

		<?php echo $this->render('//layouts/_footer'); ?>
	    </div>	
	</div>
    </section>
    
    <?= \bluezed\scrollTop\ScrollTop::widget() ?>
    
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>