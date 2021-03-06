<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Cronjob Handler Log View
 *  
 * @license GPLv3
 * 
 * @since       6.0.00
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$flash = new \app\src\Core\etsis_Messages();
$cronlog = cronDir() . 'cron/logs/cronjobs.log';
$screen = 'cron';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Cronjob Handler Log' );?></li>
</ul>

<h3><?=_t( 'Cronjob Handler Log' );?></h3>
<div class="innerLR">
    
    <?=$flash->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>
	
	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>cron/log/" id="validateSubmitForm" method="post">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li class="glyphicons dashboard"><a href="<?=get_base_url();?>cron/<?=bm();?>"><i></i> <?=_t( 'Handler Dashboard' );?></a></li>
                    <li class="glyphicons star"><a href="<?=get_base_url();?>cron/new/<?=bm();?>"><i></i> <?=_t( 'New Cronjob Handler' );?></a></li>
                    <li class="glyphicons list tab-stacked active"><a href="<?=get_base_url();?>cron/log/<?=bm();?>" data-toggle="tab"><i></i> <?=_t( 'Log' );?></a></li>
                    <li class="glyphicons wrench tab-stacked"><a href="<?=get_base_url();?>cron/setting/<?=bm();?>"><i></i> <span><?=_t( 'Settings' );?></span></a></li>
                    <!-- <li class="glyphicons circle_question_mark tab-stacked"><a href="<?=get_base_url();?>cron/about/<?=bm();?>"><i></i> <span><?=_t( 'About' );?></span></a></li> -->
                </ul>
            </div>
            <!-- // Tabs Heading END -->
            
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					
					<!-- Column -->
					<div class="col-md-12">
						
						<!-- Group -->
						<div class="form-group">
							<div class="col-md-12">
								<textarea class="col-md-12 form-control" rows="10"><?=(file_exists($cronlog) ? _file_get_contents($cronlog) : 'No log found');?></textarea>
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
                    <button type="submit" class="btn btn-icon btn-primary glyphicons circle_minus"><i></i><?=_t( 'Clear Log' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
	<div class="separator bottom"></div>
    
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>