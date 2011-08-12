<?php
namespace Model\Object;

interface inPosition {
	private $x, $y, $z;
	public function setPosition($x, $y, $z);
}