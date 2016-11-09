<?php
/*
Plugin Name: BuddyPress Avatar Bubble
Plugin URI: https://cosydale.com/plugin-cd-avatar-bubble.html
Description: After moving your mouse pointer on any avatar you will see a bubble with the defined by admin information about this user or group.
Version: 2.5.1
Author: slaFFik
Author URI: https://cosydale.com/
Network: true
*/
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

define( 'CD_AB_VERSION', '2.5.1' );
define( 'CD_AB_IMAGE_URI', WP_PLUGIN_URL . '/cd-bp-avatar-bubble/_inc/images' );

function cd_ab_activation() {
	$cd_ab['color']   = 'blue';
	$cd_ab['borders'] = 'images';

	$cd_ab['access'] = 'all';

	$cd_ab['messages'] = 'yes';
	$cd_ab['friend']   = 'no';

	$cd_ab['action'] = 'click';
	$cd_ab['delay']  = '0';

	$cd_ab['groups']['status'] = 'off';
	$cd_ab['groups']['join']   = 'off';
	$cd_ab['groups']['type']   = array( 'public' );
	$cd_ab['groups']['data']   = array( 'name', 'short_desc', 'members', 'forum_stat' );

	add_option( 'cd_ab', $cd_ab, '', 'yes' );
}

register_activation_hook( __FILE__, 'cd_ab_activation' );

function cd_ab_deactivation() {
	delete_option( 'cd_ab' );
}

register_deactivation_hook( __FILE__, 'cd_ab_deactivation' );

/**
 * Load languages.
 */
function cd_ab_load_textdomain() {
	load_plugin_textdomain( 'cd-bp-avatar-bubble', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
}

add_action( 'plugins_loaded', 'cd_ab_load_textdomain' );

// Load parts of the plugin only on proper places
if ( is_admin() ) {
	require( dirname( __FILE__ ) . '/cd-ab-admin.php' );
}
if ( ! is_admin() ) {
	require( dirname( __FILE__ ) . '/cd-ab-cssjs.php' );
}

/**
 * BUBBLE ENGINE
 *
 * @param $text
 * @param $params
 *
 * @return mixed
 */
function cd_ab_rel_filter( $text, $params ) {
	$cd_ab = get_blog_option( bp_get_root_blog_id(), 'cd_ab' );

	if ( $params['object'] == 'user' ) {
		return preg_replace( '~<img (.+?) />~i', "<img $1 rel='user_{$params['item_id']}' />", $text );
	} elseif ( $params['object'] == 'group' ) {
		if ( $cd_ab['groups']['status'] == 'on' ) {
			return preg_replace( '~<img (.+?) />~i', "<img $1 rel='group_{$params['item_id']}' />", $text );
		} else {
			return $text;
		}
	} else {
		return $text;
	}
}

add_filter( 'bp_core_fetch_avatar', 'cd_ab_rel_filter', 10, 2 );

/**
 * Display bubbe when clicking/hovering on a group avatar in activity stream.
 *
 * @param $action
 * @param $activity
 *
 * @return mixed
 */
function cd_ab_rel_activity_filter( $action, $activity ) {
	switch ( $activity->component ) {
		case 'groups' :
			$cd_ab = get_blog_option( bp_get_root_blog_id(), 'cd_ab' );
			if ( $cd_ab['groups']['status'] == 'on' ) {
				$reverse_content = strrev( $action );
				$position        = strpos( $reverse_content, 'gmi<' );
				preg_match( '~group-(\d++)-avatar~', $action, $match );
				$replace = "rel='group_{$match[1]}' ";
				$action  = substr_replace( $action, $replace, - $position + 1, 0 );
			}
			break;
	}

	return $action;
}

add_filter( 'bp_get_activity_action', 'cd_ab_rel_activity_filter', 99, 2 );

/**
 * Fork of BP Add Friend Button - need this to work with BP 1.2.
 *
 * @param bool $ID
 * @param bool $friend_status
 *
 * @return bool|mixed
 */
function cd_ab_get_add_friend_button( $ID = false, $friend_status = false ) {
	global $bp, $friends_template;

	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( ! $ID && $friends_template->friendship->friend ) {
		$ID = $friends_template->friendship->friend->id;
	} else if ( ! $ID && ! $friends_template->friendship->friend ) {
		$ID = $bp->displayed_user->id;
	}

	if ( $bp->loggedin_user->id == $ID ) {
		return false;
	}

	if ( bp_is_active( 'friends' ) ) {
		if ( empty( $friend_status ) ) {
			$friend_status = friends_check_friendship_status( $bp->loggedin_user->id, $ID );
		}
	}

	$button = '';

	if ( 'pending' == $friend_status ) {
		$button .= '<a class="requested" href="' . $bp->loggedin_user->domain . $bp->friends->slug . '/">' . __( 'Friendship Requested', 'cd-bp-avatar-bubble' ) . '</a>';
	} else if ( 'is_friend' == $friend_status ) {
		$button .= '<a href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->friends->slug . '/remove-friend/' . $ID . '/', 'friends_remove_friend' ) . '" title="' . __( 'Cancel Friendship', 'cd-bp-avatar-bubble' ) . '" id="friend-' . $ID . '" rel="remove" class="remove">' . __( 'Cancel Friendship', 'cd-bp-avatar-bubble' ) . '</a>';
	} else {
		$button .= '<a href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->friends->slug . '/add-friend/' . $ID . '/', 'friends_add_friend' ) . '" title="' . __( 'Add Friend', 'cd-bp-avatar-bubble' ) . '" id="friend-' . $ID . '" rel="add" class="add">' . __( 'Add Friend', 'cd-bp-avatar-bubble' ) . '</a>';
	}

	return apply_filters( 'cd_ab_get_add_friend_button', $button );
}

/**
 * Display everything.
 */
function cd_ab_the_avatardata() {
	$cd_ab = get_blog_option( bp_get_root_blog_id(), 'cd_ab' );
	$ID    = (int) $_GET['ID'];
	$type  = sanitize_key( $_GET['type'] );

	switch ( $type ) {
		case 'user':
			if ( $cd_ab['access'] == 'admin' && is_super_admin() ) {
				cd_ab_get_the_userdata( $ID, $cd_ab );
			} elseif ( $cd_ab['access'] == 'logged_in' && is_user_logged_in() ) {
				cd_ab_get_the_userdata( $ID, $cd_ab );
			} elseif ( $cd_ab['access'] == 'all' ) {
				cd_ab_get_the_userdata( $ID, $cd_ab );
			} else {
				echo $cd_ab['delay'] . '|~|<div id="user_' . $ID . '">' . __( 'You don\'t have enough rights to view user data', 'cd-bp-avatar-bubble' ) . '</div>';
			}
			break;
		case 'group':
			if ( $cd_ab['access'] == 'admin' && is_super_admin() ) {
				cd_ab_get_the_group_data( $ID, $cd_ab );
			} elseif ( $cd_ab['access'] == 'logged_in' && is_user_logged_in() ) {
				cd_ab_get_the_group_data( $ID, $cd_ab );
			} elseif ( $cd_ab['access'] == 'all' ) {
				cd_ab_get_the_group_data( $ID, $cd_ab );
			} else {
				echo $cd_ab['delay'] . '|~|<div id="group_' . $ID . '">' . __( 'You don\'t have enough rights to view group data', 'cd-bp-avatar-bubble' ) . '</div>';
			}
			break;
	}
	die;
}

add_action( 'wp_ajax_cd_ab_the_avatardata', 'cd_ab_the_avatardata' );
add_action( 'wp_ajax_nopriv_cd_ab_the_avatardata', 'cd_ab_the_avatardata' );

/**
 * For Groups.
 *
 * @param $ID
 * @param $cd_ab
 */
function cd_ab_get_the_group_data( $ID, $cd_ab ) {
	global $bp;
	echo $cd_ab['delay'] . '|~|<div id="group_' . $ID . '">';
	$group = groups_get_group( array( 'group_id' => $ID ) );
	if ( ! in_array( $group->status, $cd_ab['groups']['type'] ) ) {
		echo __( 'You don\'t have enough rights to view data of this group', 'cd_ab' ) . '</div>';
		die;
	}

	$group_link = $bp->root_domain . '/' . BP_GROUPS_SLUG . '/' . $group->slug;

	// Group Name
	if ( in_array( 'name', $cd_ab['groups']['data'] ) ) {
		echo '<p class="popupLine" style="padding-top:0"><a href="' . $group_link . '">' . $group->name . '</a>';
		// Group Description (shortened)
		if ( in_array( 'short_desc', $cd_ab['groups']['data'] ) ) {
			echo ' &rarr; ' . bp_create_excerpt( $group->description, 10 );
		}
		echo '</p>';
	} else { // and description only
		if ( in_array( 'short_desc', $cd_ab['groups']['data'] ) ) {
			echo '<p class="popupLine" style="padding-top:0"><a href="' . $group_link . '">#</a> ' . bp_create_excerpt( $group->description, 10 ) . '</p>';
		}
	}

	echo '<p class="popupLine">';
	// Group Status display
	$type_used = false;
	if ( in_array( 'status', $cd_ab['groups']['data'] ) ) {
		if ( 'public' == $group->status ) {
			$type = __( 'Public Group', 'cd-bp-avatar-bubble' );
		} else if ( 'hidden' == $group->status ) {
			$type = __( 'Hidden Group', 'cd-bp-avatar-bubble' );
		} else if ( 'private' == $group->status ) {
			$type = __( 'Private Group', 'cd-bp-avatar-bubble' );
		} else {
			$type = ucwords( $group->status ) . ' ' . __( 'Group', 'cd-bp-avatar-bubble' );
		}
		echo $type;
		$type_used = true;
	}

	// Formatted number of group members
	if ( in_array( 'members', $cd_ab['groups']['data'] ) ) {
		if ( 1 == $group->total_member_count ) {
			$members_data = apply_filters( 'bp_get_group_member_count', sprintf( __( '%s member', 'cd-bp-avatar-bubble' ), bp_core_number_format( $group->total_member_count ) ) );
		} else {
			$members_data = apply_filters( 'bp_get_group_member_count', sprintf( __( '%s members', 'cd-bp-avatar-bubble' ), bp_core_number_format( $group->total_member_count ) ) );
		}
		if ( $type_used ) {
			echo '<span style="float:right">' . $members_data . '</span>';
		} else {
			echo $members_data;
		}
	}
	echo '</p>';

	if ( in_array( 'join', $cd_ab['groups']['data'] ) ) {
		$button = bp_get_group_join_button( $group );
		if ( ! empty( $button ) ) {
			echo $button;
		}
	}

	// Display activity date
	if ( in_array( 'activity_date', $cd_ab['groups']['data'] ) ) {
		$activity_data = sprintf( __( 'Active %s', 'cd-bp-avatar-bubble' ), bp_core_time_since( $group->last_activity ) );
		echo '<p class="popupLine">' . $activity_data;
		if ( in_array( 'feed_link', $cd_ab['groups']['data'] ) ) {
			echo ' (<a href="' . $group_link . '/feed" target="_blank">' . __( 'RSS', 'cd-bp-avatar-bubble' ) . '</a>)';
		}
		echo '</p>';
	}

	// display the forum stat text
	if ( function_exists( 'bp_forums_is_installed_correctly' ) ) {
		if ( bp_forums_is_installed_correctly() ) {
			if ( in_array( 'forum_stat', $cd_ab['groups']['data'] ) ) {
				// get all required data for count
				$forum_id     = groups_get_groupmeta( $ID, 'forum_id' );
				$forum_counts = bp_forums_get_forum_topicpost_count( (int) $forum_id );
				if ( 1 == (int) $forum_counts[0]->topics ) {
					$total_topics = sprintf( __( '%d topic', 'cd-bp-avatar-bubble' ), (int) $forum_counts[0]->topics );
				} else {
					$total_topics = sprintf( __( '%d topics', 'cd-bp-avatar-bubble' ), (int) $forum_counts[0]->topics );
				}
				if ( 1 == (int) $forum_counts[0]->posts ) {
					$total_posts = sprintf( __( '%d post', 'cd-bp-avatar-bubble' ), (int) $forum_counts[0]->posts );
				} else {
					$total_posts = sprintf( __( '%d posts', 'cd-bp-avatar-bubble' ), (int) $forum_counts[0]->posts );
				}
				// echo the text
				echo '<p class="popupLine">' . sprintf( __( '<strong>Forum</strong>: %s and %s', 'cd-bp-avatar-bubble' ), $total_topics, $total_posts ) . '</p>';
			}
		}
	}

	echo '<div style="clear:both"></div></div>';
}

/**
 * For users.
 *
 * @param $ID
 * @param $cd_ab
 */
function cd_ab_get_the_userdata( $ID, $cd_ab ) {
	global $bp;
	$i      = 1;
	$action = 'false';
	$output = $mention = $message = $profile = $link = $class = '';

	do_action( 'cd_ab_before_default' );

	if ( $cd_ab['messages'] == 'yes' ) {
		$i ++;

		$profile .= '<strong><a href="' . bp_core_get_user_domain( $ID, false, false ) . '" title="' . __( 'Go to profile page', 'cd-bp-avatar-bubble' ) . '">#</a></strong>';

		if ( $cd_ab['action'] == 'click' ) {
			$action = 'true';
		}

		if ( is_user_logged_in() ) {
			if ( bp_is_active( 'activity' ) ) {
				$mention .= '<strong><a href="' . bp_core_get_user_domain( $bp->loggedin_user->id, false, false ) . BP_ACTIVITY_SLUG . '/?r=' . bp_core_get_username( $ID, false, false ) . '" title="' . __( 'Mention this user', 'cd-bp-avatar-bubble' ) . '">@' . bp_core_get_username( $ID, false, false ) . '</a></strong>';
			}
			if ( bp_is_active( 'messages' ) ) {
				$message .= '<a href="' . bp_core_get_user_domain( $bp->loggedin_user->id, false, false ) . BP_MESSAGES_SLUG . '/compose/?r=' . bp_core_get_username( $ID, false, false ) . '" title="' . __( 'Send a private message to this user', 'cd-bp-avatar-bubble' ) . '">' . __( 'Private Message', 'cd-bp-avatar-bubble' ) . '</a>';
			}
		} else {
			if ( bp_is_active( 'activity' ) ) {
				$mention .= '<strong><a href="' . $bp->root_domain . '/wp-login.php?redirect_to=' . urlencode( $bp->root_domain ) . '" title="' . __( 'You should be logged in to mention this user', 'cd-bp-avatar-bubble' ) . '">@' . bp_core_get_username( $ID, false, false ) . '</a></strong>';
			}
			if ( bp_is_active( 'messages' ) ) {
				$message .= '<strong><a href="' . $bp->root_domain . '/wp-login.php?redirect_to=' . urlencode( $bp->root_domain ) . '" title="' . __( 'You should be logged in to send a private message', 'cd-bp-avatar-bubble' ) . '">' . __( 'Private Message', 'cd-bp-avatar-bubble' ) . '</a></strong>';
			}
		}
		if ( empty( $message ) && empty( $mention ) ) {
			$profile .= ' | @' . bp_core_get_username( $ID, false, false );
		}
		$output .= '<p class="popupLine" style="padding-top:0px">' . $profile . ( bp_is_active( 'activity' ) ? ' | ' . $mention : '' ) . ( bp_is_active( 'messages' ) ? ' | ' . $message : '' ) . '</p>';
	}

	if ( $cd_ab['friend'] == 'yes' && $ID != $bp->loggedin_user->id && is_user_logged_in() && bp_is_active( 'friends' ) ) {
		$i ++;
		if ( $i != 1 ) {
			$class = ' style="padding-top:6px;"';
		}
		if ( $cd_ab['action'] == 'click' && $action == 'false' ) {
			$link = '<strong><a href="' . bp_core_get_user_domain( $ID, false, false ) . '" title="' . __( 'Go to profile page', 'cd-bp-avatar-bubble' ) . '">#</a> | </strong>';
		}
		$output .= '<p class="popupLine"' . $class . '>' . $link . cd_ab_get_add_friend_button( $ID, false ) . '</p>';
	}

	do_action( 'cd_ab_before_fields', $ID );

	// get visibility levels - array (key - field_id, value - visibility level: public|loggedin|friends)
	$vis_levels = get_user_option( 'bp_xprofile_visibility_levels', $ID );

	foreach ( $cd_ab as $field_id => $field_data ) {
		global $field;
		$field = xprofile_get_field( $field_id );

		if ( $vis_levels && isset( $vis_levels[ $field_id ] ) ) {
			if ( $vis_levels[ $field_id ] == 'loggedin' && ! is_user_logged_in() ) {
				continue;
			}
			if ( $vis_levels[ $field_id ] == 'friends' && ! friends_check_friendship( $ID, $bp->loggedin_user->id ) ) {
				continue;
			}
			do_action( 'cd_ab_check_xprofile_fields_visibility', $ID, $vis_levels );
		}

		if ( ! empty( $field_data['name'] ) && is_numeric( $field_id ) ) {
			$field_value = xprofile_get_field_data( $field_id, $ID );

			if ( $field_value != null ) {
				if ( $field_data['type'] == 'multiselectbox' || $field_data['type'] == 'checkbox' ) {
					$field_value = bp_unserialize_profile_field( $field_value );
				}
				if ( $field_data['type'] == 'datebox' && $field_value != null ) {
					if ( strpos( $field_value, '-' ) ) {
						$field_value = bp_format_time( strtotime( $field_value ), true );
					} else {
						$field_value = bp_format_time( bp_unserialize_profile_field( $field_value ), true );
					}
				}
				if ( $i != 1 ) {
					$class = ' style="padding-top:6px;"';
				}

				if ( isset( $field_data['link'] ) && $field_data['link'] == 'yes' ) {
					if ( is_array( $field_value ) ) {
						$field_value = implode( ',', $field_value );
					}
					$field_link = xprofile_filter_link_profile_data( $field_value, $field_data['type'] );
					$field_link = apply_filters( 'cd_ab_field_link', $field_link, $ID, $field_id, $field_data['type'], $field_value );
				} else {
					$field_link = is_array( $field_value ) ? implode( ", ", $field_value ) : $field_value;
					$field_link = apply_filters( 'cd_ab_field_text', $field_link, $ID, $field_id, $field_data['type'], $field_value );
				}
				$output .= '<p class="popupLine"' . $class . '><strong>' . $field_data['name'] . '</strong>: ' . $field_link . '</p>';
			}
			$i ++;
		}
	}

	unset( $field );

	$output = apply_filters( 'cd_ab_output', $output );

	do_action( 'cd_ab_after_default' );

	if ( empty( $output ) ) {
		$output = __( 'Nothing to display. Check a bit later please.', 'cd-bp-avatar-bubble' );
	}

	echo "<div id='user_$ID'>$output<div style='clear:both'></div></div>";
}
