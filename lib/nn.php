<?php
// Other techniques for learning

class ActivationFunction {
  function __construct($func, $dfunc) {
      $this->func = $func;
      $this->dfunc = $dfunc;
  }
}

//requires php 7.4 or later
global $sigmoid;
global $tanh;
$sigmoid = new ActivationFunction(fn($x) => 1 / (1 + exp(-$x)), fn($y) => $y * (1 - $y));

$tanh = new ActivationFunction(fn($x) => tanh($x), fn($y) => 1 - ($y * $y));

//trying to shut errors up
class Matrix{}
$Matrix = new Matrix;

class NeuralNetwork {
  /*
  * if first argument is a NeuralNetwork the constructor clones it
  * USAGE: cloned_nn = new NeuralNetwork(to_clone_nn);
  */
  function __construct($in_nodes, $hid_nodes, $out_nodes) {
    $this->sigmoid = new ActivationFunction(fn($x) => 1 / (1 + exp(-$x)), fn($y) => $y * (1 - $y));
    $this->tanh = new ActivationFunction(fn($x) => tanh($x), fn($y) => 1 - ($y * $y));
    if ($in_nodes instanceof NeuralNetwork) {
      $a = $in_nodes;
      $this->input_nodes = $a->input_nodes;
      $this->hidden_nodes = $a->hidden_nodes;
      $this->output_nodes = $a->output_nodes;

      $this->weights_ih = $a->weights_ih;
      $this->weights_ho = $a->weights_ho;

      $this->bias_h = $a->bias_h;
      $this->bias_o = $a->bias_o;
    } else {
      $this->input_nodes = $in_nodes;
      $this->hidden_nodes = $hid_nodes;
      $this->output_nodes = $out_nodes;

      $this->weights_ih = new Matrix($this->hidden_nodes, $this->input_nodes);
      $this->weights_ho = new Matrix($this->output_nodes, $this->hidden_nodes);
      $this->weights_ih->randomize();
      $this->weights_ho->randomize();

      $this->bias_h = new Matrix($this->hidden_nodes, 1);
      $this->bias_o = new Matrix($this->output_nodes, 1);
      $this->bias_h->randomize();
      $this->bias_o->randomize();
    }

    // TODO: copy these as well
    $this->setLearningRate();
    $this->setActivationFunction(null);


  }

  function predict($input_array) {

    // Generating the Hidden Outputs
    $inputs = $Matrix->fromArray($input_array);
    $hidden = $Matrix->multiply($this->weights_ih, $inputs);
    $hidden->add($this->bias_h);
    // activation function!
    $hidden->map($this->activation_function->func);

    // Generating the output's output!
    $output = $Matrix->multiply($this->weights_ho, $hidden);
    $output->add($this->bias_o);
    $output->map($this->activation_function->func);

    // Sending back to the caller!
    return (array) $output;
  }

  function setLearningRate($learning_rate = 0.1) {
    $this->learning_rate = $learning_rate;
  }

  function setActivationFunction($func) {
    if (!is_null($func)){
      $f = $func;
    } else {
      $f = $this->sigmoid;
    }
    $this->activation_function = $f;
  }

  function train($input_array, $target_array) {
    // Generating the Hidden Outputs
    $inputs = $Matrix->fromArray($input_array);
    $hidden = $Matrix->multiply($this->weights_ih, $inputs);
    $hidden->add($this->bias_h);
    // activation function!
    $hidden->map($this->activation_function->func);

    // Generating the output's output!
    $outputs = $Matrix->multiply($this->weights_ho, $hidden);
    $outputs->add($this->bias_o);
    $outputs->map($this->activation_function->func);

    // Convert array to matrix object
    $targets = $Matrix->fromArray($target_array);

    // Calculate the error
    // ERROR = TARGETS - OUTPUTS
    $output_errors = $Matrix->subtract($targets, $outputs);

    // $gradient = outputs * (1 - outputs);
    // Calculate gradient
    $gradients = $Matrix->map($outputs, $this->activation_function->dfunc);
    $gradients->multiply($output_errors);
    $gradients->multiply($this->learning_rate);


    // Calculate deltas
    $hidden_T = $Matrix->transpose($hidden);
    $weight_ho_deltas = $Matrix->multiply($gradients, $hidden_T);

    // Adjust the weights by deltas
    $this->weights_ho->add($weight_ho_deltas);
    // Adjust the bias by its deltas (which is just the gradients)
    $this->bias_o->add($gradients);

    // Calculate the hidden layer errors
    $who_t = $Matrix->transpose($this->weights_ho);
    $hidden_errors = $Matrix->multiply($who_t, $output_errors);

    // Calculate hidden gradient
    $hidden_gradient = $Matrix->map($hidden, $this->activation_function->dfunc);
    $hidden_gradient->multiply($hidden_errors);
    $hidden_gradient->multiply($this->learning_rate);

    // Calcuate input->hidden deltas
    $inputs_T = $Matrix->transpose($inputs);
    $weight_ih_deltas = $Matrix->multiply($hidden_gradient, $inputs_T);

    $this->weights_ih->add($weight_ih_deltas);
    // Adjust the bias by its deltas (which is just the gradients)
    $this->bias_h->add($hidden_gradient);

    // outputs.print();
    // targets.print();
    // error.print();
  }

  function serialize() {
    //return JSON.stringify(this); // javascript version
    return json_encode(get_object_vars($this)); // I think this will work in php
  }

  static function deserialize($data) {
    if (is_string($data)) {
      $data = json_decode($data);
    }
    $nn = new NeuralNetwork($data->input_nodes, $data->hidden_nodes, $data->output_nodes);
    $nn->weights_ih = $Matrix->deserialize($data->weights_ih);
    $nn->weights_ho = $Matrix->deserialize($data->weights_ho);
    $nn->bias_h = $Matrix->deserialize($data->bias_h);
    $nn->bias_o = $Matrix->deserialize($data->bias_o);
    $nn->learning_rate = $data->learning_rate;
    return $nn;
  }


  // Adding function for neuro-evolution
  /* PHP: use $nn2 = clone $nn;
  function copy() {
    return new NeuralNetwork($this);
  }
*/
  // Accept an arbitrary function for mutation
  function mutate($func) {
    $this->weights_ih->map($func);
    $this->weights_ho->map($func);
    $this->bias_h->map($func);
    $this->bias_o->map($func);
  }



}
?>
