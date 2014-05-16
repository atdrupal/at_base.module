<?php
namespace Drupal\at_base\Helper;

/**
 * Simple class for benchmark.
 *
 * Usage:
 *
 * @code
 *   $timer = atcg('helper.timer');
 *
 *   // Set the callback — code to be benchmarked
 *   $timer->setCallback(function() { $modules = at_modules('at_base'); });
 *
 *   // Run it 10 times
 *   $timer->setTimes(10);
 *
 *   // Profile the callback with xhprof
 *   // If we defined $conf['at_xhprof_domain'] and $conf['at_xhprof_root'] in
 *   // settings.php, then we just need callback setXProfConfig with empty array
 *   // $timer->setXProfConfig()
 *   $timer->setXProfConfig(array('domain' => 'http://xhprof.example.com/', 'root' => '/var/www/'));
 *
 *   // Now, start benchmarking
 *   $data = $timer->run();
 * @code
 */
class Timer {
  private $callback;
  private $times = 1;
  private $memory = TRUE;
  private $memory_start = 0;
  private $memory_peak_start = 0;
  private $xhprof_config = array();

  /**
   * @param  callable  $callback
   */
  public function setCallback($callback) {
    $this->callback = $callback;
  }

  /**
   * @param int $times
   */
  public function setTimes($times) {
    $this->times = $times;
  }

  /**
   * @param boolean $memory
   */
  public function setMemory($memory) {
    $this->memory = $memory;
  }

  public function setXProfConfig($xhprof_config = array()) {
    $this->xhprof_config = $xhprof_config;

    if (empty($this->xhprof_config['domain'])) {
      if ($domain = at_valid('at_xhprof_domain', TRUE)) {
        $this->xhprof_config['domain'] = $domain;
      }
    }

    if (empty($this->xhprof_config['root'])) {
      if ($root = at_valid('at_xhprof_root', TRUE)) {
        $this->xhprof_config['root'] = $root;
      }
    }
  }

  public function run() {
    // Start timer, memory, profile
    // memory_get_peak_usage
    if ($this->memory) {
      $this->memory_start = memory_get_usage();
      $this->memory_peak_start = memory_get_peak_usage();
    }

    timer_start('ATimer');
    $this->profileStart();

    for ($i = 1; $i <= $this->times; ++$i) {
      call_user_func($this->callback);
    }

    // Stop timer, memory, profile
    $return  = array();
    $return += $this->profileStop();
    $return += timer_stop('ATimer');
    if ($this->memory) {
      $return += array(
        'memory' => number_format(memory_get_usage() - $this->memory_start),
        'memory_peak' => number_format(memory_get_peak_usage() - $this->memory_peak_start),
      );
    }

    // Tell user the result
    return $return;
  }

  private function profileStart() {
    if (empty($this->xhprof_config)) return;
    xhprof_enable();
  }

  private function profileStop() {
    if (empty($this->xhprof_config)) return array();

    $xhprof_data = xhprof_disable();
    $config = $this->xhprof_config;

    require_once $config['root'] . "/xhprof_lib/utils/xhprof_lib.php";
    require_once $config['root'] . "/xhprof_lib/utils/xhprof_runs.php";

    $xhprof_runs = new \XHProfRuns_Default();
    $run_id = $xhprof_runs->save_run($xhprof_data, 'AndyTruong');

    return array(
      'xhpref' => "{$config['domain']}/index.php?run={$run_id}&source=AndyTruong",
    );
  }
}
