<?php

interface RouterUrls{
    public function add(string $url, string $handler);
    public function route();
}

class StructCurrentUrl {
    public string $url;
    public array|string $url_segments;
    public array $slug_pos;
}

trait ParseUrl{
    private array $slug_values = array();

    /**
     * Writes slug field data to a variable.
     * @param array $values An associative array that contains this slug field data.
     * @return void
     */
    private function save_slug_args(array $values): void{
        foreach ($values as $key => $value){
            $this->slug_values[$key] = $value;
        }
    }

    /**
     * In an array of common paths, searches for the current path. If it is found, it returns its name.
     *
     * Only searches for normal addresses without parameters.
     * @param array $urls A shared array of url.
     * @param string $current_url Current page address.
     * @return string Address or blank string.
     */
    protected function search_url(array $urls, string $current_url): string{
        foreach ($urls as $path => $_){
            if ($path == $current_url){
                return $path;
            }
        }
        return "";
    }

    /**
     * Search for urls with at least one slug parameter. Returns an array of these addresses.
     * @param array $urls A shared array of url.
     * @param array $current_path_segments Partitioned current page address.
     * @return array Array of addresses with found slug parameter.
     */
    protected function search_urls_with_slug(array $urls, array $current_path_segments): array {
        $struct_correct_url_array = array();
        foreach ($urls as $key_u => $_){
            $struct_curr_url = new StructCurrentUrl();
            if (strpos($key_u, "{")){
                $struct_curr_url->url = $key_u;
            } else{
                continue;
            }

            // Add address to array if length matches and special characters are found.
            $u = url_segmentation($key_u);
            $struct_curr_url->url_segments = $u;
            if (count($u) == count($current_path_segments)){
                for ($i = 0; $i < count($u); $i++) {
                    if ($u[$i][0] == "{"){
                        $struct_curr_url->slug_pos[] = $i;
                    }
                }
                $struct_correct_url_array[] = $struct_curr_url;
            }
        }
        return $struct_correct_url_array;
    }

    /**
     * Compares the current address and the address with parameters.
     *
     * Also writes slug parameters to an associative array.
     * @param array $current_path_segments Partitioned current page address.
     * @param array|StructCurrentUrl $urls Addresses with slug parameters.
     * @return string Returns a matching address pattern or an empty string if one is not found.
     */
    protected function compare_url_to_path(array $current_path_segments, array|StructCurrentUrl $urls): string {
        for ($i = 0; $i < count($urls); $i++) {
            $new_url = $urls[$i]->url_segments;
            $slug_args = array();
            $u_pos = $urls[$i]->slug_pos;
            for ($j = 0; $j < count($u_pos); $j++) {
                $slug_args[trim($new_url[$u_pos[$j]], "{} ")] = $current_path_segments[$u_pos[$j]];
                $new_url[$u_pos[$j]] = $current_path_segments[$u_pos[$j]];
            }
            if (count(array_diff($new_url, $current_path_segments)) == 0){
                $this->save_slug_args($slug_args);
                return $urls[$i]->url;
            }
        }
        return "";
    }
}

class XRouter implements RouterUrls {
    use ParseUrl;

    private array $urls;
    public string $current_path;

    /**
     * @param string $current_path Current page address.
     */
    public function __construct(string $current_path)
    {
        $this->current_path = $current_path;
    }

    /**
     * Adds the address to the shared list.
     * @param string $url URL pattern.
     * @param string $handler Path to handler.
     * @return void
     */
    public function add(string $url, string $handler): void{
        $this->urls[$url] = $handler;
    }

    /**
     * The main method that starts all routing methods.
     * @return void
     */
    public function route(): void{
        $current_path_segments = url_segmentation($this->current_path);
        $urls = $this->search_urls_with_slug($this->urls, $current_path_segments);

        $default_url = $this->search_url($this->urls, $this->current_path);
        if ($default_url != "") {
            include getenv("PATH_TO_SRC") . $this->urls[$default_url];
        }
        $slug_url = $this->compare_url_to_path($current_path_segments, $urls);
        if ($slug_url != ""){
            foreach ($this->slug_values as $key => $value){
                $_GET[$key] = "AAA";
            }
            include getenv("PATH_TO_SRC") . $this->urls[$slug_url];
        }
        exit();
    }
}

/**
 *  Splits any url into parts.
 * @param string $url
 * @return array|bool
 */
function url_segmentation(string $url): array|bool {
    return preg_split('[/]', $url, 0, PREG_SPLIT_NO_EMPTY);
}

function get_requested_url(): string{
    $requested_url = $_SERVER['REQUEST_URI'];
    $query_pos = strpos($requested_url, '?');

    if ($query_pos !== false) {
        $requested_url = substr($requested_url, 0, $query_pos);
    }
    return $requested_url;
}
