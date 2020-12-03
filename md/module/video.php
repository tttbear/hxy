<?php 
	if(function_exists('getPlayVipAdPv')){
		$vod_id = get_post_meta(get_the_ID(),'vod_id',true);
		if($vod_id){
			echo '<div class="single-video">'.do_shortcode('[erphpvideo]').'</div>';
		}
	}else{
		$player = _MBT('post_video_player')?_MBT('post_video_player'):'ckplayer';
		echo '<script src="'.get_bloginfo('template_url').'/module/'.$player.'/'.$player.'.min.js"></script>';
		$video = get_post_meta(get_the_ID(),'video',true);
		$video_type = get_post_meta(get_the_ID(),'video_type',true);
		if(wp_is_erphpdown_active()){
			$video_erphpdown = get_post_meta(get_the_ID(),'video_erphpdown',true);
			$memberDown=get_post_meta(get_the_ID(), 'member_down',true);
			$days=get_post_meta(get_the_ID(), 'down_days', true);
			$price=get_post_meta(get_the_ID(), 'down_price', true);
			$start_down2=get_post_meta(get_the_ID(), 'start_down2', true);
			$userType=getUsreMemberType();
			if($video){
				if($video_erphpdown){
					$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
					$wppay = new EPD(get_the_ID(), $user_id);
					$erphp_url_front_vip = add_query_arg('action','vip',get_permalink(MBThemes_page("template/user.php")));

					if($start_down2){
						if($wppay->isWppayPaid() || !$price || ($memberDown == 3 && $userType)){
							if($video_type){
		    					echo '<div class="single-video"><iframe src="'.$video.'" class="'.$player.'-video"></iframe></div>';
		    				}else{
			    				$nonce = wp_create_nonce(rand(10,1000));
			    				echo '<div class="single-video"><div id="'.$player.'-video-'.$nonce.'" class="'.$player.'-video '.$player.'-video-real" data-nonce="'.$nonce.'" data-video="'.trim($video).'"></div></div>';
							}
						}else{
							$video_content = '';
							if($memberDown == 3){
								$video_content .= '此视频观看价格为<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即支付</a>&nbsp;&nbsp;<b>或</b>&nbsp;&nbsp;升级VIP后免费<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
							}else{
								$video_content .= '此视频观看价格为<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即支付</a>';	
							}

							echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">'.$video_content.'</div></div></div>';
						}
					}else{
						if(is_user_logged_in()){
							$user_info=wp_get_current_user();
							$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
							if($days > 0){
								$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
								$nowDate = date('Y-m-d H:i:s');
								if(strtotime($nowDate) > strtotime($lastDownDate)){
									$down_info = null;
								}
							}
							if( (($memberDown==3 || $memberDown==4) && $userType) || $wppay->isWppayPaid() || $down_info || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9) && $userType == 10) ){

								if(!$wppay->isWppayPaid() && !$down_info){
									$erphp_life_times    = get_option('erphp_life_times');
									$erphp_year_times    = get_option('erphp_year_times');
									$erphp_quarter_times = get_option('erphp_quarter_times');
									$erphp_month_times  = get_option('erphp_month_times');
									$erphp_day_times  = get_option('erphp_day_times');
									if(checkDownHas($user_info->ID,get_the_ID())){
										if($video_type){
					    					echo '<div class="single-video"><iframe src="'.$video.'" class="'.$player.'-video"></iframe></div>';
					    				}else{
						    				$nonce = wp_create_nonce(rand(10,1000));
						    				echo '<div class="single-video"><div id="'.$player.'-video-'.$nonce.'" class="'.$player.'-video '.$player.'-video-real" data-nonce="'.$nonce.'" data-video="'.trim($video).'"></div></div>';
										}
									}else{
										if($userType == 6 && $erphp_day_times > 0){
											if( checkSeeLog($user_info->ID,get_the_ID(),$erphp_day_times,erphpGetIP()) ){
												echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">您可免费观看此视频！<a href="javascript:;" class="erphpdown-vip erphpdown-see-btn" data-post="'.get_the_ID().'">立即观看</a>（今日已观看'.getSeeCount($user_info->ID).'个，还可观看'.($erphp_day_times-getSeeCount($user_info->ID)).'个）</div></div></div>';
											}else{
												echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">您暂时无权观看此视频，请明天再来！（今日已观看'.getSeeCount($user_info->ID).'个，还可观看'.($erphp_day_times-getSeeCount($user_info->ID)).'个）</div></div></div>';
											}
										}elseif($userType == 7 && $erphp_month_times > 0){
											if( checkSeeLog($user_info->ID,get_the_ID(),$erphp_month_times,erphpGetIP()) ){
												echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">您可免费观看此视频！<a href="javascript:;" class="erphpdown-vip erphpdown-see-btn" data-post="'.get_the_ID().'">立即观看</a>（今日已观看'.getSeeCount($user_info->ID).'个，还可观看'.($erphp_month_times-getSeeCount($user_info->ID)).'个）</div></div></div>';
											}else{
												echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">您暂时无权观看此视频，请明天再来！（今日已观看'.getSeeCount($user_info->ID).'个，还可观看'.($erphp_month_times-getSeeCount($user_info->ID)).'个）</div></div></div>';
											}
										}elseif($userType == 8 && $erphp_quarter_times > 0){
											if( checkSeeLog($user_info->ID,get_the_ID(),$erphp_quarter_times,erphpGetIP()) ){
												echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">您可免费观看此视频！<a href="javascript:;" class="erphpdown-vip erphpdown-see-btn" data-post="'.get_the_ID().'">立即观看</a>（今日已观看'.getSeeCount($user_info->ID).'个，还可观看'.($erphp_quarter_times-getSeeCount($user_info->ID)).'个）</div></div></div>';
											}else{
												echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">您暂时无权观看此视频，请明天再来！（今日已观看'.getSeeCount($user_info->ID).'个，还可观看'.($erphp_quarter_times-getSeeCount($user_info->ID)).'个）</div></div></div>';
											}
										}elseif($userType == 9 && $erphp_year_times > 0){
											if( checkSeeLog($user_info->ID,get_the_ID(),$erphp_year_times,erphpGetIP()) ){
												echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">您可免费观看此视频！<a href="javascript:;" class="erphpdown-vip erphpdown-see-btn" data-post="'.get_the_ID().'">立即观看</a>（今日已观看'.getSeeCount($user_info->ID).'个，还可观看'.($erphp_year_times-getSeeCount($user_info->ID)).'个）</div></div></div>';
											}else{
												echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">您暂时无权观看此视频，请明天再来！（今日已观看'.getSeeCount($user_info->ID).'个，还可观看'.($erphp_year_times-getSeeCount($user_info->ID)).'个）</div></div></div>';
											}
										}elseif($userType == 10 && $erphp_life_times > 0){
											if( checkSeeLog($user_info->ID,get_the_ID(),$erphp_life_times,erphpGetIP()) ){
												echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">您可免费观看此视频！<a href="javascript:;" class="erphpdown-vip erphpdown-see-btn" data-post="'.get_the_ID().'">立即观看</a>（今日已观看'.getSeeCount($user_info->ID).'个，还可观看'.($erphp_life_times-getSeeCount($user_info->ID)).'个）</div></div></div>';
											}else{
												echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">您暂时无权观看此视频，请明天再来！（今日已观看'.getSeeCount($user_info->ID).'个，还可观看'.($erphp_life_times-getSeeCount($user_info->ID)).'个）</div></div></div>';
											}
										}else{
											if($video_type){
						    					echo '<div class="single-video"><iframe src="'.$video.'" class="'.$player.'-video"></iframe></div>';
						    				}else{
							    				$nonce = wp_create_nonce(rand(10,1000));
							    				echo '<div class="single-video"><div id="'.$player.'-video-'.$nonce.'" class="'.$player.'-video '.$player.'-video-real" data-nonce="'.$nonce.'" data-video="'.trim($video).'"></div></div>';
											}
										}
									}
								}else{
				    				if($video_type){
				    					echo '<div class="single-video"><iframe src="'.$video.'" class="'.$player.'-video"></iframe></div>';
				    				}else{
					    				$nonce = wp_create_nonce(rand(10,1000));
					    				echo '<div class="single-video"><div id="'.$player.'-video-'.$nonce.'" class="'.$player.'-video '.$player.'-video-real" data-nonce="'.$nonce.'" data-video="'.trim($video).'"></div></div>';
									}
								}
							}else{
								$video_content = '';
								if($price){
									if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
										$video_content.='此视频观看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
								}else{
									if($memberDown != 4 && $memberDown != 8 && $memberDown != 9){
										if($video_type){
					    					echo '<div class="single-video"><iframe src="'.$video.'" class="'.$player.'-video"></iframe></div>';
					    				}else{
						    				$nonce = wp_create_nonce(rand(10,1000));
						    				echo '<div class="single-video"><div id="'.$player.'-video-'.$nonce.'" class="'.$player.'-video '.$player.'-video-real" data-nonce="'.$nonce.'" data-video="'.trim($video).'"></div></div>';
										}
									}	
								}
								
								if($price || $memberDown == 4 || $memberDown == 8 || $memberDown == 9){
									if($memberDown > 1)
									{
										$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
										if($userType){
											$vipText = '';
										}
										if($memberDown==3 && $down_info==null){
											$video_content.='（VIP免费'.$vipText.'）';
										}elseif ($memberDown==2 && $down_info==null){
											$video_content.='（VIP 5折'.$vipText.'）';
										}elseif ($memberDown==5 && $down_info==null){
											$video_content.='（VIP 8折'.$vipText.'）';
										}elseif ($memberDown==6 && $down_info==null){
											if($userType < 9){
												$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级年费VIP</a>';
											}
											$video_content.='（年费VIP免费'.$vipText.'）';
										}elseif ($memberDown==7 && $down_info==null){
											if($userType < 10){
												$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级终身VIP</a>';
											}
											$video_content.='（终身VIP免费'.$vipText.'）';
										}elseif ($memberDown==4){
											if($userType){
												
											}
										}
									}

									if($memberDown==4 && $userType==FALSE){
										$video_content.='此视频仅限VIP观看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
									}elseif($memberDown==8 && $userType<9)
									{
										$video_content.='此视频仅限年费VIP观看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
									}elseif($memberDown==9 && $userType<10)
									{
										$video_content.='此视频仅限终身VIP观看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
									}
									else 
									{
										
										if($userType && $memberDown > 1)
										{
											if ($memberDown==2 && $down_info==null)
											{
												$video_content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
											}
											elseif ($memberDown==5 && $down_info==null)
											{
												$video_content.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
											}
											elseif ($memberDown==6 && $down_info==null)
											{
												if($userType < 9){
													$video_content.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
												}
											}
											elseif ($memberDown==7 && $down_info==null)
											{
												if($userType < 10){
													$video_content.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
												}
											}
											
										}
										else 
										{
											$video_content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
										}
									}
									echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">'.$video_content.'</div></div></div>';
								}
							}
						}elseif($wppay->isWppayPaid()){
							if($video_type){
		    					echo '<div class="single-video"><iframe src="'.$video.'" class="'.$player.'-video"></iframe></div>';
		    				}else{
			    				$nonce = wp_create_nonce(rand(10,1000));
			    				echo '<div class="single-video"><div id="'.$player.'-video-'.$nonce.'" class="'.$player.'-video '.$player.'-video-real" data-nonce="'.$nonce.'" data-video="'.trim($video).'"></div></div>';
							}
						}else{
							$video_content = '';
							if($memberDown == 4 || $memberDown == 8 || $memberDown == 9){
								$video_content.='此视频仅限VIP观看，请先<a href="javascript:;" class="erphp-login-must signin-loader">登录</a>';
							}else{
								if($price){
									$video_content.='此视频观看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
									if($memberDown > 1){
										$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
										if($userType){
											$vipText = '';
										}
										if($memberDown==3 && $down_info==null){
											$video_content.='（VIP免费'.$vipText.'）';
										}elseif ($memberDown==2 && $down_info==null){
											$video_content.='（VIP 5折'.$vipText.'）';
										}elseif ($memberDown==5 && $down_info==null){
											$video_content.='（VIP 8折'.$vipText.'）';
										}elseif ($memberDown==6 && $down_info==null){
											if($userType < 9){
												$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级年费VIP</a>';
											}
											$video_content.='（年费VIP免费'.$vipText.'）';
										}elseif ($memberDown==7 && $down_info==null){
											if($userType < 10){
												$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级终身VIP</a>';
											}
											$video_content.='（终身VIP免费'.$vipText.'）';
										}elseif ($memberDown==4){
											if($userType){
												
											}
										}
									}

									$video_content.='，请先<a href="javascript:;" class="erphp-login-must signin-loader">登录</a>';
								}else{
									$video_content.='此资源可免费观看，请先<a href="javascript:;" class="erphp-login-must signin-loader">登录</a>';
								}
							}
							echo '<div class="single-video"><div class="'.$player.'-video '.$player.'-erphpdown-video"><div class="playicon"><i class="icon icon-play"></i></div><div class="erphpdown erphpdown-see erphpdown-content-vip" id="erphpdown" style="display:block">'.$video_content.'</div></div></div>';
						}
					}
				}else{
					if($video_type){
						echo '<div class="single-video"><iframe src="'.$video.'" class="'.$player.'-video"></iframe></div>';
					}else{
						$nonce = wp_create_nonce(rand(10,1000));
						echo '<div class="single-video"><div id="'.$player.'-video-'.$nonce.'" class="'.$player.'-video '.$player.'-video-real" data-nonce="'.$nonce.'" data-video="'.trim($video).'"></div></div>';
					}
				}
			}
		}else{
			if($video){
				if($video_type){
					echo '<div class="single-video"><iframe src="'.$video.'" class="'.$player.'-video"></iframe></div>';
				}else{
    				$nonce = wp_create_nonce(rand(10,1000));
    				echo '<div class="single-video"><div id="'.$player.'-video-'.$nonce.'" class="'.$player.'-video '.$player.'-video-real" data-nonce="'.$nonce.'" data-video="'.trim($video).'"></div></div>';
				}
			}
		}
	}
?>