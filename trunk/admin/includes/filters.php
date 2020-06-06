<?php
add_filter('the_content', 'recruitly_wordpress_job_application_form');

/**
 * Injects job application button
 * @param $content
 * @return string
 */
function recruitly_wordpress_job_application_form($content)
{

    global $post;

    if ($post->post_type == 'recruitlyjobs') {

        $val = get_post_custom_values('jobId');
        $jobId='';

        if ($val!==null)
        {
            if (is_array($val)){
                $jobId = $val[0];
            }else{
                $jobId = $val;
            }

            $content .= '<div class="cool-jobview-footer" style="padding-bottom:25px;font-weight: bold;"><a target="_blank" class="cool-apply-btn btn" data-featherlight="iframe" data-featherlight-iframe-frameborder="0" data-featherlight-iframe-height="635" data-featherlight-iframe-width="450" href="https://recruitly.io/jobs/widget/apply/' . $jobId . '">Apply</a></div>';
        }

    }

    return $content;
}