<div id="slide-collapse" class="page-sidebar navbar-collapse collapse " >
    <ul class="page-sidebar-menu-tool">
        <li>
            <div class="sidebar-user">
                <?php
                /** @var dektrium\user\models\User $user */
                $user = Yii::$app->user->identity;
                $username = (empty($user->profile->name)) ? '' : $user->profile->name;
                ?>
                <a href="<?= \yii\helpers\Url::to(['/user/settings/profile']) ?>">
                    <?php if (!Yii::$app->user->isGuest): ?>
                        <img class="img-circle" src="http://gravatar.com/avatar/<?= $user->profile->gravatar_id ?>?s=36" alt="<?= $user->username ?>"/>
                    <?php else: ?>
                        <img class="img-circle" src="<?= Yii::getAlias('@storageUrl') . '/images/nouser.png' ?>"/> 
                    <?php endif; ?>
                    <span class="title"> &nbsp; Hi <?= (!Yii::$app->user->isGuest) ? $username : 'Guest' ?></span>
                </a>
            </div>
        </li>
        <li class="sidebar-toggler-item">
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <div class="fa fa-bars sidebar-toggler hidden-phone"></div>
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
        </li>
    </ul>
    <!-- BEGIN SIDEBAR MENU -->  
    <?php
    $moduleID = '';
    $controllerID = '';
    $actionID = '';

    if (isset(Yii::$app->controller->module->id)) {
        $moduleID = Yii::$app->controller->module->id;
    }
    if (isset(Yii::$app->controller->id)) {
        $controllerID = Yii::$app->controller->id;
    }
    if (isset(Yii::$app->controller->action->id)) {
        $actionID = Yii::$app->controller->action->id;
    }

    backend\components\AppComponent::sidebarMenu($moduleID, $controllerID, $actionID);
    ?>
    <?=
    \appxq\sdii\widgets\Sidebar::widget([
        'firstItemCssClass' => 'start',
        'lastItemCssClass' => 'last',
        'items' => Yii::$app->params['sidebar'],
    ]);
    ?>
    <!-- END SIDEBAR MENU -->
</div>