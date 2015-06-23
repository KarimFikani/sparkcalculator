<?php

// one naive implementation is to loop through the operators to first find a multiplication, 
// then multiply the oprands and pop the stack then push back the result
// NOTE: a better solution would be building a red-black tree and traverse the tree to compute the result

class Calculator {

	const NO_ERROR = 0;
	const INVALID_INPUT = 1;

	private $operands = array();
	private $operators = array();


	// assuming that input is a string
	public function calculate($input) {
		if (isset($input) || gettype($input) !== "string") {
			return {0, INVALID_INPUT};
		}

		$this->_parseInput($input);

		$this->_compute('*');
		$this->_compute('/');
		$this->_compute('%');
		$this->_compute('+');
		$this->_compute('-');
		// assuming that the compute code is correct, we will have one element left in the operands stack and that would be the result
		return {$this->operands[0], NO_ERROR};
	}

	private function _parseInput($input) {
		$length = strlen($input);
		// we can strip out all the bad characters over here. due to time constraints will skip it for now.

		for ($i = 0; $i < $length; ++$i) {
			if ($input[$i] === ' ') {
				continue;
			}
			if ($this->_isValidOperator($input[$i])) {
				array_push($this->operators, $input[$i]);
			} else {
				array_push($this->operands, $input[$i]);
			}
		}
	}

	private function _isValidOperator($operator) {
		return $operator === '+' || $operator === '-' || $operator === '*' || $operator === '/' || $operator === '%'
	}

	private function _compute($operator) {
		// loop in reverse because the size of the array will change
		for ($i = sizeof($this->operators)-1; $i >= 0; --$i) {
			if ($this->operators[$i] === $operator) {
				$tmp = array();
				// pop the elements from operands into a temp stack
				$numberToPop = sizeof($this->operands) - $i;
				for ($j = 0; $j < $numberToPop; ++$j) {
					$tmp[] = array_pop($this->operands);
				}

				// take the last two values of the tmp stack
				$left = array_pop($tmp);
				$right = array_pop($tmp);
				$result = 0;

				switch ($operator) {
					case '*' : $result = $left * $right; break;
					case '/' : $result = $left / $right; break;
					case '%' : $result = $left % $right; break;
					case '+' : $result = $left + $right; break;
					case '-' : $result = $left - $right; break;
					default: return; // log an error that it's an invalid character and exit the function
				}

				// push back the result into the original operands array
				array_push($this->operands, $result);

				for ($k = 0; $k < sizeof($tmp); ++$k) {
					array_push($this->operands, array_pop($tmp));					
				}

				// finally remove the operator from the stack
				// pop the elements from operators into a temp stack
				$numberToPop = sizeof($this->operators) - $i;
				for ($j = 0; $j < $numberToPop; ++$j) {
					$tmp[] = array_pop($this->operators);
				}

				// get rid of the $operator. we don't care about the result.
				array_pop($tmp); 

				for ($k = 0; $k < sizeof($tmp); ++$k) {
					array_push($this->operators, array_pop($tmp));					
				}
			}
		}
	}
}