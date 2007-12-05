<?php
/**
 * Class for time measurements
 *
 * Created through refactoring function timer() in functions.php
 *
 * usage example:
 * <code>
 * $timer = new Timer();
 * //doesn't work: $timer->end = microtime();
 * $timer->stop();
 * echo $timer; // uses __toString() method
 * </code>
 */
class Timer {

    /**
     * Start time
     * @var string
     */
    protected $start;

    /**
     * End time
     * @var string
     */
    protected $end;

    /**
     * Creates a new timer
     * @return MyTimer
     */
    public function __construct() {
        $this->start = microtime();
    }

    /**
     * Stops the measurement
     * @return void
     */
    public function stop() {
        $this->end = microtime();
    }

    /**
     * Calculates the time difference even if the timer hasn't been stopped yet
     *
     * @return float Measured time difference
     */
    public function getTime() {
        return self::subtractMicrotimes($this->start, $this->end);
    }

    /**
     * Stops the measurement and calculates the time difference even if the
     * timer has not been stopped yet
     *
     * @return float Measured time difference
     */
    public function stopAndGetTime() {
        $this->stop();
        return $this->getTime();
    }

    /**
     * Converts instance to a string
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->getTime();
    }

    /**
     * Substracts two microtimes
     *
     * @param string $start Output of microtime()
     * @param string $end
     *        Output of microtime()
     *        Can be obeyed to use the current time
     */
    static public function subtractMicrotimes($start, $end = null) {
        if (empty($end)) {
            $end = microtime();
        }
        list($usecStart, $secStart) = explode(' ', $start);
        list($usecEnd, $secEnd) = explode(' ', $end);
        $seconds = (int) $secEnd - (int) $secStart;
        $useconds = (float) $usecEnd - (float) $usecStart;
        return (float) $seconds + $useconds;
    }
}
?>
