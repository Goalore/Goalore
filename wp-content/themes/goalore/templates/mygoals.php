<?php 
  /**
    *
    *Template Name: My Goals
    *
    */

get_header(); 

$userID = get_current_user_id(); 

$mygoals = New WP_Query([
    'post_type'       => 'goals',
    'author'          => $userID, 
    'posts_per_page'  => -1,
    'meta_query'      => [
        'goal_status' => [ 'key' => 'goal_status' ],
        'target'      => [ 'key' => 'target' ],
        [ [
            'key'     => 'archive',
            'compare' => 'NOT EXISTS'
        ],
        'relation'    => 'OR',
        [
            'key'     => 'archive',
            'value'   => '1',
            'compare' => '!='
        ] ]
    ], 
    'orderby'         => [
        'goal_status' => 'DESC',
        'target'      => 'ASC',
    ]
]); $CountMyGoals = $mygoals->found_posts;


$followedgoals = New WP_Query([
    'post_type'       => 'goals',
    'posts_per_page'  => -1,
    'meta_query'      => [
        [
            'key'     => 'followers',
            'value'   => '"' . $userID . '"',
            'compare' => 'LIKE',
        ],
        'goal_status' => [ 'key' => 'goal_status' ],
        'target'      => [ 'key' => 'target' ],

    ],
    'orderby'         => [
        'goal_status' => 'DESC',
        'target'      => 'ASC',
    ]
]); $CountFollowedGoals = $followedgoals->found_posts;


$active_goal_admin = '';
$active_alliance_follower = '';
if(isset($_REQUEST['follower'])){
    $active_alliance_follower = 'show active';
}else{
    $active_goal_admin = 'show active';
}


?>
<section class="goal-desktop-sec">
    <div class="container">
        <div class="row align-items-center goal-header">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="create-goal">
                    <a href="javascript:;"  data-toggle="modal" data-target="#createGoal" class="btn btn-blue">Create New Goal</a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <ul class="nav goal-catg" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link btn <?php echo $active_goal_admin; ?>" id="mygoal-tab" data-toggle="tab" href="#mygoal" role="tab" aria-controls="mygoal" aria-selected="true">
                            My Goals (<?php echo$CountMyGoals ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn <?php echo $active_alliance_follower; ?>" id="followedgoal-tab" data-toggle="tab" href="#followedgoal" role="tab" aria-controls="followedgoal" aria-selected="false">
                            Followed Goals (<?php echo$CountFollowedGoals ?>)
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade <?php echo $active_goal_admin; ?>" id="mygoal" role="tabpanel" aria-labelledby="mygoal-tab">
                <div class="row">
                    <?php if($mygoals->have_posts()){
                while($mygoals->have_posts()) { 
                  $mygoals->the_post();
                  get_template_part('template-parts/goal','content'); 
                } wp_reset_query();
              }else{ ?>
                    <div class="col-12 text-center ">
                        <div class="alert alert-warning">
                            No Goals found!
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="tab-pane fade <?php echo $active_alliance_follower; ?> " id="followedgoal" role="tabpanel" aria-labelledby="followedgoal-tab">
                <div class="row">
                    <?php  
                  if($followedgoals->have_posts()){
                    while($followedgoals->have_posts()) { 
                      $followedgoals->the_post();
                      get_template_part('template-parts/goal','content'); 
                    } wp_reset_query();
                  }else{ ?>
                    <div class="col-12 text-center ">
                        <div class="alert alert-warning">
                            You are currently not following any goals. Use "Quick Search" or "Search Goals" link to find and follow goals.
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade " id="createGoal" tabindex="-1" role="dialog" aria-labelledby="createGoalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header tex">
                <h5 class="modal-title" id="createGoalLabel">Create A New Goal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body  ">
                <div class="container">
                    <div class="row justify-content-md-center">
                        <div class="col-lg-10 col-12">
                            <div class="newmc-form-body">
                                <div class="gcard-form create-goal-form">
                                    <form id="create-goal-frm" method="post">
                                        <!-- <h6>Create a new goal!</h6> -->
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="title" id="title" class="form-control" >
                                        </div>
                                        <div class="form-group">
                                            <label>Type of Goal</label>
                                            <select class="form-control" name="type" id="type">
                                                <option value="individual">Individual Goal</option>
                                                <option value="community">Community Goal</option>
                                            </select>
                                        </div>
                                        <div class="form-group ">
                                            <label>Target Date of Completion</label>
                                            <input type="date" placeholder="yyyy-mm-dd" min="<?php echo date('Y-m-d'); ?>"  name="target" id="target" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Goal Category</label>
                                            <select name="category" class="form-control" id="category" >
                                                <option value="">Select</option>
                                                <?php $terms = get_terms([
                                                      'taxonomy'   => 'goal_categories',
                                                      'hide_empty' => false,
                                                      'parent'   => 0,
                                                      'meta_key' => 'active',
                                                      'meta_value' => '1'
                                                    ]); if(!empty($terms)){
                                                        foreach($terms as $term){ ?>
                                                            <option value="<?php echo $term->term_id; ?>">
                                                                <?php echo $term->name ?>
                                                            </option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Goal Subcategory</label>
                                            <select class="form-control" name="subcategory" id="sub-category" >
                                                <option value=""></option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Privacy Status</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="private">Keep it private for now</option>
                                                <option value="public">Make this public to everyone</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-blue">Create Goal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php get_footer(); ?>