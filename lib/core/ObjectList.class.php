<?php
	class ObjectList {
		protected $list = array();
		protected $limit = -1;
		protected $offset = 0;
		protected $total_count = 0;
		protected $sort_by = "default";
		protected $order = "asc";
		
		public function setList($list) {
			if(is_array($list)) {
				$this->list = $list;
			}
		}
		
		public function setLimit($limit) {
			if(is_int($limit))
				$this->limit = $limit;
		}
		
		public function setOffset($offset) {
			if(is_int($offset))
				$this->offset = $offset;
		}
		
		public function setTotalCount($total_count) {
			if(is_int($total_count))
				$this->total_count = $total_count;
		}
		
		public function setSortBy($sort_by) {
			if(is_string($sort_by))
				$this->sort_by = $sort_by;
		}
		
		public function setOrder($order) {
			if($order=="asc" OR $order=="desc")
				$this->order = $order;
		}
		
		public function getList() {
			return $this->list;
		}
		
		public function getLimit() {
			return $this->limit;
		}
		
		public function getOffset() {
			return $this->offset;
		}
		
		public function getSortBy() {
			return $this->sort_by;
		}
		
		public function getTotalCount() {
			return $this->total_count;
		}
		
		public function getOrder() {
			return $this->order;
		}
		
		public function delete() {
			foreach($this->getList() as $item) {
				$item->delete();
			}
		}
	}
?>