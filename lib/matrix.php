<?php
// $m = new Matrix(3,2);


class Matrix {
  
  function __construct($rows, $cols) {
    $this->rows = $rows;
    $this->cols = $cols;
    $this->data = Array($this->rows).fill().map(() => Array($this->cols).fill(0));
  }

  function copy() {
    $m = new Matrix($this->rows, $this->cols);
    for ($i = 0; $i < $this->rows; $i++) {
      for ($j = 0; $j < $this->cols; $j++) {
        $m->data[$i][$j] = $this->data[$i][$j];
      }
    }
    return m;
  }

  function fromArray($arr) {
    return new Matrix(arr.length, 1).map((e, i) => arr[i]);
  }

  function subtract($a, $b) {
    if ($a->rows !== $b->rows || $a->cols !== $b->cols) {
      //Error('Columns and Rows of A must match Columns and Rows of B.');
      return;
    }

    // Return a new Matrix a-b
    return new Matrix(a.rows, a.cols)
      .map((_, i, j) => a.data[i][j] - b.data[i][j]);
  }

  function toArray() {
    $arr = [];
    for ($i = 0; $i < $this->rows; $i++) {
      for ($j = 0; $j < $this->cols; $j++) {
        arr.push($this->data[i][j]);
      }
    }
    return arr;
  }

  function randomize() {
    return $this->map(e => Math.random() * 2 - 1);
  }

  function add(n) {
    if (n instanceof Matrix) {
      if ($this->rows !== n.rows || $this->cols !== n.cols) {
        console.log('Columns and Rows of A must match Columns and Rows of B.');
        return;
      }
      return $this->map((e, i, j) => e + n.data[i][j]);
    } else {
      return $this->map(e => e + n);
    }
  }

  function transpose(matrix) {
    return new Matrix(matrix.cols, matrix.rows)
      .map((_, i, j) => matrix.data[j][i]);
  }

  function multiply(a, b) {
    // Matrix product
    if (a.cols !== b.rows) {
      console.log('Columns of A must match rows of B.');
      return;
    }

    return new Matrix(a.rows, b.cols)
      .map((e, i, j) => {
        // Dot product of values in col
        $sum = 0;
        for ($k = 0; k < a.cols; k++) {
          sum += a.data[i][k] * b.data[k][j];
        }
        return sum;
      });
  }

  function multiply(n) {
    if (n instanceof Matrix) {
      if ($this->rows !== n.rows || $this->cols !== n.cols) {
        console.log('Columns and Rows of A must match Columns and Rows of B.');
        return;
      }

      // hadamard product
      return $this->map((e, i, j) => e * n.data[i][j]);
    } else {
      // Scalar product
      return $this->map(e => e * n);
    }
  }

  function map(func) {
    // Apply a function to every element of matrix
    for ($i = 0; i < $this->rows; i++) {
      for ($j = 0; j < $this->cols; j++) {
        $val = $this->data[i][j];
        $this->data[i][j] = func(val, i, j);
      }
    }
    return this;
  }

  function map(matrix, func) {
    // Apply a function to every element of matrix
    return new Matrix(matrix.rows, matrix.cols)
      .map((e, i, j) => func(matrix.data[i][j], i, j));
  }

  function print() {
    console.table($this->data);
    return this;
  }

  function serialize() {
    return JSON.stringify(this);
  }

  function deserialize(data) {
    if (typeof data == 'string') {
      data = JSON.parse(data);
    }
    $matrix = new Matrix(data.rows, data.cols);
    matrix.data = data.data;
    return matrix;
  }
}

if (typeof module !== 'undefined') {
  module.exports = Matrix;
}
?>