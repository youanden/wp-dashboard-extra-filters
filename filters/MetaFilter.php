<?php if (!defined('WPINC')) die();

class dashboardExtraFilters_MetaFilter {
	
	public $post_types = array();
	public $empty_label = null;
	public $meta_key = null;
	
	public function __construct() {
		if (!$this->empty_label) {
			$this->empty_label = __('Show All',DASHBOARD_EXTRA_FILTERS_PLUGIN);
		}
		
		add_filter('query_vars', array(&$this,'add_query_vars_filter'));
		add_action('restrict_manage_posts', array(&$this,'show_filters'));
		add_action('parse_query', array(&$this,'apply_filters'));
		
		
	}
	
	// add query var
		public function add_query_vars_filter($vars) {
			$vars[] = $this->meta_key;
			return $vars;
		}
	
	// show filters
		public function show_filters() {
			global $typenow;
			
			if (in_array($typenow, $this->post_types)) {
								
				// filtering 
					$meta_values = dashboardExtraFiltersModel::getDistinctMetaValues($this->post_types, $this->meta_key);
					
				?>
					<select class="js-dashboard-extra-filters" name="<?php echo $this->meta_key; ?>">
						<option value="0"><?php echo $this->empty_label; ?></option>
						<?php
						
							foreach($meta_values as $meta_value) {
								$if_selected = '';
								if (get_query_var($this->meta_key) == $meta_value) {
									$if_selected = 'selected="selected"';
								}
						
								?>
									<option <?php echo $if_selected; ?> value="<?php echo $meta_value;?>"><?php echo $meta_value;?></option>
								<?php
							}
						?>
					</select>
				<?php
			}
		}
	
	// apply filters
		public function apply_filters( $query ) {
			global $pagenow, $typenow;
			
			$qv = &$query->query_vars;
	
			if ('edit.php' == $pagenow && get_query_var($this->meta_key)) {
				
				if (!isset($qv['meta_query'])) {
					$qv['meta_query'] = array();
				}
				
				$qv['meta_query'][] = array(
					'field' => $this->meta_key,
					'value' => get_query_var($this->meta_key),
					'compare' => '=',
					'type' => ''
				);
			}
		}
	
	
}