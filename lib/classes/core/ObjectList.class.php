<?php
	class ObjectList {
		protected $limit = 100;
		protected $offset = 0;
		protected $total_count = 0;
		
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
		
		public function getLimit() {
			return $this->limit;
		}
		
		public function getOffset() {
			return $this->offset;
		}
		
		public function getTotalCount() {
			return $this->total_count;
		}
	}
?>