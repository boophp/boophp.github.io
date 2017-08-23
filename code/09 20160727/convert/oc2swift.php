<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>将 OC 转换成为 Swift </title>
        <meta charset="UTF-8">
        <script type="text/javascript" src="http://s3.ucai.cn/js/jquery-5aa9d86bc8.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>

            <?php
            /*
             * To change this license header, choose License Headers in Project Properties.
             * To change this template file, choose Tools | Templates
             * and open the template in the editor.
             */

            if (!empty($_REQUEST))
            {
                $content = $_POST['code'];
                // echo $code;
            } else
            {
                if ($argc < 2)
                {
                    echo "php {$argv[0]} <filename>\n";
                    exit;
                } else
                {
                    $content = file_get_contents($argv[1]);
                }
            }

            echo "OC 代码：<pre  class=\"brush:oc;toolbar:false\">";
            echo htmlspecialchars($content);
            echo "</pre>";
            echo "Swift 代码：<pre class=\"brush:swift;toolbar:false\">";
            $content = convertVars($content);
            $content = convertHeader($content);
            $content = convertClass($content);
            $content = convertClassMethd($content);
            $content = convertClassCall($content);
            $content = constructCall($content);
            $content = clearLines($content);
            $content = methodCall($content);
            echo htmlspecialchars($content);
            echo "</pre>";
            /* 变量定义替换 */

            function convertVars($content)
            {
                /* UIProgressView* prog;
                  NSTimer* timer; */
                $lines = explode("\n", $content);
                //print_r($lines);
                $ret = "";
                foreach ($lines as $line)
                {
                    $line = preg_replace("#@property(.*?)\)#", "", $line);
                    $tline = trim($line);
                    if (preg_match_all("#^(\w+)\s+\*?\s*(\w+);#", $tline, $result) || preg_match_all("#^(\w+)\s*\*?\s+(\w+);#", $tline, $result))
                    {
                        //  print_r($result);
                        $line = str_replace($result[0][0], "var " . $result[2][0] . ":" . $result[1][0] . "!", $line);
                    } else if (preg_match_all("#^(\w+)\s+(\w+);#", $tline, $result) || preg_match_all("#(NS\w+)\s+(\w+);#", $tline, $result))
                    {
                        //  print_r($result);
                        $line = str_replace($result[0][0], "var " . $result[2][0] . ":" . $result[1][0] . "!", $line);
                    }
                    $ret .= $line . "\n";
                }
                return $ret;
            }

            /* #import <Foundation/Foundation.h> 
              #import <CoreLocation/CoreLocation.h>
              #import <UIKit/UIKit.h> */

            function convertHeader($content)
            {
                $lines = explode("\n", $content);
                $ret = "";
                foreach ($lines as $line)
                {

                    if (strpos($line, "#import") !== false && strpos($line, "/") !== false)
                    {
                        $line = substr($line, 0, strpos($line, "/"));
                        $line = str_replace("\"", "", $line);
                        $line = str_replace("<", "", $line);
                        $line = str_replace("#", "", $line);
                    } else if (strpos($line, "#import") !== false)
                    {
                        continue;
                    }
                    $ret .= $line . "\n";
                }
                return $ret;
            }

            /* 类定义，类方法替换， */
            /* 替换 IBAction 相关 */

            function convertClass($content)
            {


                /* #import "FKViewController.h"

                  @interface FKViewController ()

                  @end

                  @implementation FKViewController
                 * 
                 * #import "FKViewController.h"
                  #import <CoreLocation/CoreLocation.h>
                  @interface FKViewController () <CLLocationManagerDelegate>
                  @property (nonatomic , retain) CLLocationManager *locationManager;
                  @property (nonatomic , retain) CLLocation *prevLocation;
                  @property (nonatomic , assign) CGFloat sumDistance;
                  @property (nonatomic , assign) CGFloat sumTime;
                  @end
                  @implementation FKViewController
                 *  */

                $interfacepattern = "#@interface(.*?)\<(.*?)>(.*?)@end(.*?)@implementation\s+(\w+)(.*?)@end#mis";
                if (preg_match_all($interfacepattern, $content, $result))
                {
                    foreach ($result[0] as $key => $item)
                    {
                        $addition = "";
                        if (strpos($result[6][$key], "viewDidLoad") !== false)
                        {
                            $addition = ",UIViewController";
                        }
                        $newcontent = "class " . $result[5][$key] . ":" . $result[2][$key] . $addition . "\n"
                                . "{"
                                .
                                $result[3][$key] . "\n" .
                                $result[6][$key] . "\n"
                                . "}";
                        $content = str_replace($result[0][$key], $newcontent, $content);
                    }
                    //print_r($result);
                }
                $interfacepattern_full = "#@interface\s+(\w+)\s*:\s*(\w+)(.*?)@end(.*?)@implementation\s+(\w+)(.*?)@end#mis";

                $interfacepattern = "#@interface\s+(\w+)(.*?)@end(.*?)@implementation\s+(\w+)(.*?)@end#mis";


                if (preg_match_all($interfacepattern_full, $content, $result))
                {

                    foreach ($result[0] as $key => $item)
                    {
                        $addition = "";
                        if (strpos($result[6][$key], "viewDidLoad") !== false)
                        {
                            $addition = ",UIViewController";
                        }
                        $newcontent = "class " . $result[5][$key] . ":" . $result[2][$key] . $addition . ""
                                . "{"
                                .
                                $result[3][$key] . "\n" .
                                $result[6][$key] . "\n"
                                . "}";
                        $content = str_replace($result[0][$key], $newcontent, $content);
                    }
                    //print_r($result);
                } else
                {
                    if (preg_match_all($interfacepattern, $content, $result))
                    {

                        foreach ($result[0] as $key => $item)
                        {
                            $addition = "";
                            if (strpos($result[5][$key], "viewDidLoad") !== false)
                            {
                                $addition = ":UIViewController";
                            }
                            $newcontent = "class " . $result[4][$key] . $addition . "\n"
                                    . "{"
                                    .
                                    $result[2][$key] . "\n" .
                                    $result[5][$key] . "\n"
                                    . "}";
                            $content = str_replace($result[0][$key], $newcontent, $content);
                        }
                        //print_r($result);
                    }
                }
                return $content;
            }

            function convertClassMethd($content)
            {



                $class_method_pattern = "#\+\s*\((\w+)\s*\)\s*(\w+):(.*?)\{#mis";
                $content = _convertMethodWithParam($content, $class_method_pattern);
                //普通方法
                $method_pattern = "#\-\s*\((\w+)\s*\)\s*(\w+):(.*?)\{#mis";
                $content = _convertMethodWithParam($content, $method_pattern);

                $class_method_no_para_pattern = "#\+\s*\((\w+)\s*\*?\s*\)\s*(\w+)\s*\{#mis";
                $content = _convertMethodNoParam($content, $class_method_pattern);
//-(NSString *)description{
                $method_no_para_pattern = "#\-\s*\((\w+)\s*\*?\s*\)\s*(\w+)\s*\{#mis";
                $content = _convertMethodNoParam($content, $method_no_para_pattern);

                return $content;
                /* - (void) viewDidLoad {
                  [super viewDidLoad];
                  // 创建CLLocationManager对象
                  self.locationManager = [[CLLocationManager alloc] init];
                  } */
            }

            function _convertMethodWithParam($content, $method_pattern)
            {
                //有参数

                if (preg_match_all($method_pattern, $content, $result))
                {
                    foreach ($result[0] as $key => $item)
                    {
                        $repl_item = str_replace("\n", "", $item);
                        $repl_item = str_replace("\r", "", $repl_item);

                        $content = str_replace($item, $repl_item . "\n", $content);
                    }
                }

                //echo $content;
                $lines = explode("\n", $content);
                //print_r($lines);
                $result = array();
                $ret = "";
                // [0] => (CLLocationManager *)manager
                //didFailWithError:(NSError *)error
                $var_fullpattern = "#(\w+)\s*:\s*\((\w+)\s*\)(\w+)#";
                // (void)locationManager:(CLLocationManager *)manager
                //didFailWithError:(NSError *)error
                $var_pattern = "#\((\w+)\s*\)(\w+)#";
                foreach ($lines as $line)
                {
                    $tline = trim($line);
                    if ($tline == "- (void) viewDidLoad")
                    {
                        $line = "override func viewDidLoad()";
                    } else
                    {
                        if (preg_match_all($method_pattern, $line, $result))
                        {
                            $pars = str_replace("*", "", $result[3][0]);

                            if (preg_match_all($var_fullpattern, $pars, $presult))
                            {
                                foreach ($presult[0] as $key => $par)
                                {
                                    $pars = str_replace($presult[0][$key], $presult[3][$key] . ":" . $presult[2][$key] . ",", $pars);
                                }
                            }

                            if (preg_match_all($var_pattern, $pars, $presult))
                            {
                                foreach ($presult[0] as $key => $par)
                                {
                                    $pars = str_replace($presult[0][$key], $presult[2][$key] . ":" . $presult[1][$key] . ",", $pars);
                                }
                            }
                            $pars = trim($pars, ",");
                            if ($result[1][0] == "void")
                                $result[1][0] = "";
                            else if ($result[1][0] == "id")
                                $result[1][0] = "AnyObject";
                            else
                            {
                                $result[1][0] = "->" . $result[1][0];
                            }

                            $line = "func " . $result[2][0] . "($pars)" . $result[1][0] . "\n{";
                        }
                    }
                    $ret .=$line . "\n";
                    //+(id)studentWithAge:(int) age{
                }
                return $ret;
            }

            function _convertMethodNoParam($content, $method_pattern)
            {
                //无参数
                if (preg_match_all($method_pattern, $content, $result))
                {
                    foreach ($result[0] as $key => $item)
                    {
                        $repl_item = str_replace("\n", "", $item);
                        $content = str_replace($item, $repl_item . "\n", $content);
                    }
                }

                //echo $content;
                $lines = explode("\n", $content);
                //print_r($lines);
                $result = array();
                $ret = "";
                foreach ($lines as $line)
                {
                    $tline = trim($line);
                    if ($tline == "- (void) viewDidLoad")
                    {
                        $line = "override func viewDidLoad()";
                    } else
                    {
                        if (preg_match_all($method_pattern, $line, $result))
                        {
                            if ($result[1][0] == "void")
                                $result[1][0] = "";
                            else if ($result[1][0] == "id")
                                $result[1][0] = "AnyObject";
                            else
                            {
                                $result[1][0] = "->" . $result[1][0];
                            }
                            if ($result[2][0] == "dealloc")
                                $result[2][0] = "deinit";
                            $line = "func " . $result[2][0] . "($pars)" . $result[1][0] . "\n{";
                        }
                    }
                    $ret .=$line . "\n";
                    //+(id)studentWithAge:(int) age{
                }
                return $ret;
            }

            /* 替换类构造方法、方法调用 */

            function convertClassCall($content)
            {

                $lines = explode("\n", $content);
                //print_r($lines);
                $ret = "";
                $call_pattern = "#^\[(\w+)\s+(\w+)\]#";

                $alloc_pattern = "#\[\[\[(\w+)\s+alloc\]\s*init\]\s*autorelease\]#";
                $alloc_pattern1 = "#\[\[(\w+)\s+alloc\]\s*init\]#";


                $first_var = "#^(\w+)\s+\*?\s*(\w+)\s*=#";
                $second_var = "#^(\w+)\s*\*?\s+(\w+)\s*=#";

                foreach ($lines as $line)
                {
                    $tline = trim($line);
                    if (preg_match_all($alloc_pattern, $tline, $result) || preg_match_all($alloc_pattern1, $tline, $result))
                    {
                        foreach ($result[0] as $key => $item)
                        {
                            $line = str_replace($item, $result[1][0] . "()", $line);
                        }
                    } else if (preg_match_all($call_pattern, $tline, $result))
                    {
                        foreach ($result[0] as $key => $item)
                        {
                            $line = str_replace($item, $result[1][0] . "." . $result[2][0] . "()", $line);
                        }
                    }

                    if (preg_match_all($first_var, trim($line), $result) || preg_match_all($second_var, trim($line), $result))
                    {
                        foreach ($result[0] as $key => $item)
                        {
                            $line = str_replace($item, "\nvar " . $result[2][$key] . " = ", $line);
                        }
                    }


                    $ret .= $line . "\n";
                }
                return $ret;
            }

            function constructCall($content)
            {
                //[NSString stringWithFormat:@"name:%@ age:%i创建了",_name,_age];
                // UITableView *myTableView = [[UITableView alloc] 
                //initWithFrame:CGRectZero style:UITableViewStyleGrouped];
                $with_pattern = "#\[(\w+)\s+(\w+)With(\w+):(.*?)\]#mis";
                $init_pattern = "#\[\[(\w+)\s+alloc\]\s*initWith(\w+):(.*?)\]#mis";
                if (preg_match_all($init_pattern, $content, $result) || preg_match_all($with_pattern, $content, $result))
                {

                    foreach ($result[0] as $key => $item)
                    {
                        $repl_item = str_replace("\n", "", $item);
                        $content = str_replace($item, $repl_item . "\n", $content);
                    }
                }
                $lines = explode("\n", $content);
                $ret = "";
                foreach ($lines as $line)
                {
                    $tline = trim($line);
                    if (preg_match_all($init_pattern, $tline, $result))
                    {
                        foreach ($result[0] as $key => $item)
                        {
                            $result[3][$key] = str_replace("@\"", "\"", $result[3][$key]);
                            $result[2][$key] = lcfirst($result[2][$key]);
                            $line = str_replace($item, $result[1][$key] . "(" . $result[2][$key] . ":" . $result[3][$key] . ")", $line);
                        }
                    } else if (preg_match_all($with_pattern, $content, $result))
                    {
                        foreach ($result[0] as $key => $item)
                        {
                            $result[4][$key] = str_replace("@\"", "\"", $result[4][$key]);
                            $result[3][$key] = lcfirst($result[3][$key]);
                            $line = str_replace($item, $result[1][$key] . "(" . $result[3][$key] . ":" . $result[4][$key] . ")", $line);
                        }
                    }
                    $ret .=$line . "\n";
                }

                return $ret;
            }

            function methodCall($content)
            {
                $call_pattern = "#\[([\w\.]+)\s+(.*?)]#mis";
                if (preg_match_all($call_pattern, $content, $result))
                {
                    foreach ($result[0] as $key => $item)
                    {
                        $repl_item = str_replace("\n", "", $item);
                        $content = str_replace($item, $repl_item . "\n", $content);
                    }
                }
                $lines = explode("\n", $content);
                $ret = "";
                foreach ($lines as $line)
                {
                    $tline = trim($line);
                    if (preg_match_all($call_pattern, $tline, $result))
                    {
                        // print_r($result);
                        foreach ($result[0] as $key => $item)
                        {
                            if (strpos($result[2][$key], ":") !== false)
                            {
                                $pos = strpos($result[2][$key], ":");
                                $method = substr($result[2][$key], 0, $pos);
                                $left = substr($result[2][$key], $pos + 1);
                                $line = str_replace($item, $result[1][$key] . "." . $method . "(" . $left . ")", $line);
                            } else
                            {
                                $line = str_replace($item, $result[1][$key] . "." . $result[2][$key] . "()", $line);
                            }
                        }
                    }
                    $ret .=$line . "\n";
                }

                return $ret;
            }

            function clearLines($content)
            {
                $lines = explode("\n", $content);
                $ret = "";
                foreach ($lines as $line)
                {
                    if (strpos($line, "release()") !== false)
                    {
                        continue;
                    }
                    $line = str_replace("NSLog(@\"", "println(\"", $line);
                    $line = str_replace("dealloc()", "deinit", $line);
                    $line = str_replace("YES", "true\"", $line);
                    $line = str_replace("NO", "false\"", $line);
                    $line = rtrim($line, " \t\n\r\0\x0B");
                    $line = rtrim($line, ";");
                    $ret .=$line . "\n";
                }

                return $ret;
            }

//初始化替代
//Objective-C
            /* UITableView *myTableView = [[UITableView alloc] 
              initWithFrame:CGRectZero style:UITableViewStyleGrouped]; */

            /* UIBarButtonItem* bn3 = [[UIBarButtonItem alloc]
              initWithBarButtonSystemItem:UIBarButtonSystemItemAdd
              target:self
              action:@selector(clicked:)]; */

            /* 	timer = [NSTimer scheduledTimerWithTimeInterval:0.2
              target:self selector:@selector(changeProgress)
              userInfo:nil repeats:YES]; */

//调用方法替换 
            /* ///Swift
              myTextField.textColor = UIColor.darkGrayColor()
              myTextField.text = "Hello world"
              if myTextField.editing {
              myTextField.editing = false
              } */


//类方法、@IBAction、@IBOutlet替代
            /* #import "FKViewController.h"

              @interface FKViewController ()

              @end

              @implementation FKViewController */

//布尔变量替换、变量声明替代
            /* UIProgressView* prog;
              NSTimer* timer; */
            ?>

        </div>
    </body>
    <link rel="stylesheet" type="text/css" href="http://www.ucai.cn/public/js/editor/third-party/SyntaxHighlighter/shCoreDefault.css"/>
    <script type="text/javascript" charset="utf-8" src="http://www.ucai.cn/public/js/editor/third-party/SyntaxHighlighter/shCore.js"></script>
    <script type="text/javascript">

        (function() {
            $(document).ready(function()
            {
                SyntaxHighlighter.highlight();
                var ri;
                for (var i = 0, di; di = SyntaxHighlighter.highlightContainers[i++]; ) {
                    var tds = di.getElementsByTagName('td');
                    for (var j = 0, li, ri; li = tds[0].childNodes[j]; j++) {
                        ri = tds[1].firstChild.childNodes[j];
                        ri.style.height = li.style.height = ri.scrollHeight + 'px';
                    }
                }
            });
        })();
    </script>
</html>