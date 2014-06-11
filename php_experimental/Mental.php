<?php
/*
		            ,+?=:                                
                ~ZZZZZZZZZ7               ,::           
              ?ZZZZZ7$ZZZZ7$:         ,7ZZZZZZZZ=       
             IZZZZZZZZZZZZZZZ=       ZI$ZZZZ$ZZZZZ.     
            :ZZZZ$ZZZZZZZ?IIZZ.     IZZZ77ZZZZ$7ZZZ.    
            ZZZZZZZZZZ7ZZZI  ~7.   7Z7ZZZZZZ$ZZZ7ZZZ    
           :ZZZZ7ZZZZ$ZZZ.     .  :ZZZZZZZZZZ7ZZZ$ZZ7   
           ?ZZZZ$ZZZZZZZZ         Z7Z?$ZZZZZZZZZZZZZZ   
           IZZZZ$ZZZZZZZZ      , ,Z     .ZZZZZ7ZZZ$ZZ.  
           +ZZZZ7ZZZZZZZZZ:   ZZ ~:      IZZZZZIZZIZZ:  
           .ZZZZ$ZZZZ$ZZZZZZZZZI ~.      =ZZZZZ7ZZ7ZZ.  
            ZZZZZ?ZZZZIZZZZZZZ?  .7      ZZZZZZ7ZZZZZ   
             ZZZZZIZZZZZ77$II?    ZZ: .~ZZZZZZIZZZZZI   
             .ZZZZZZZZZZZZZ7Z     =$ZZZZZZZZZZZZZIZZ    
               =ZZZZZZZ$7ZZ7       7IZZZZZZZZZZZIZZ,    
                 :ZZZZZZI,          ~Z$ZZZZIZZZ$ZZ,     
                                      =$ZZZZ$ZZZI       
                                         .+I?,       
MENTALJS USAGE
-----------------------------------

REWRITING
-----------------------------------
$js = new MentalJS;
try {	
	echo $js->rewrite('abc'') . '<br>';
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), '\n';
}

SYNTAX CHECKING
-----------------------------------
$js = new MentalJS;
try {
	$js->parse('1+1');
} catch (Exception $e){}
if($js->isValid()) {
	echo 'Is valid JS';
} else {
	echo 'Invalid JS';
} 

PARSE TREE
-----------------------------------
$js = new MentalJS;
echo $js->getParseTree('1+1');  

MINIFY
-----------------------------------
$js = new MentalJS;
echo $js->minify('function      x (    ) {\n\n\n\n\n x      =      1\n3\n\n}');
*/

mb_internal_encoding("UTF-8");

class MentalJS {			
	const SQUARE_OPEN = 91; const SQUARE_CLOSE = 93; const PAREN_OPEN = 40; const PAREN_CLOSE = 41;
	const CURLY_OPEN = 123; const CURLY_CLOSE = 125;
	const LOWER_A = 97; const LOWER_B = 98; const LOWER_C = 99; const LOWER_D = 100; const LOWER_E = 101;
	const LOWER_F = 102; const LOWER_G = 103; const LOWER_H = 104; const LOWER_I = 105; const LOWER_J = 106;
	const LOWER_K = 107; const LOWER_L = 108; const LOWER_M = 109; const LOWER_N = 110; const LOWER_O = 111;
	const LOWER_P = 112; const LOWER_Q = 113; const LOWER_R = 114; const LOWER_S = 115; const LOWER_T = 116;
	const LOWER_U = 117; const LOWER_V = 118; const LOWER_W = 119; const LOWER_X = 120; const LOWER_Y = 121;
	const LOWER_Z = 122;
	const UPPER_A = 65; const UPPER_B = 66; const UPPER_C = 67; const UPPER_D = 68; const UPPER_E = 69;
	const UPPER_F = 70; const UPPER_G = 71; const UPPER_H = 72; const UPPER_I = 73; const UPPER_J = 74;
	const UPPER_K = 75; const UPPER_L = 76; const UPPER_M = 77; const UPPER_N = 78; const UPPER_O = 79;
	const UPPER_P = 80; const UPPER_Q = 81; const UPPER_R = 82; const UPPER_S = 83; const UPPER_T = 84;
	const UPPER_U = 85; const UPPER_V = 86; const UPPER_W = 87; const UPPER_X = 88; const UPPER_Y = 89;
	const UPPER_Z = 90;
	const DIGIT_0 = 48; const DIGIT_1 = 49; const DIGIT_2 = 50; const DIGIT_3 = 51; const DIGIT_4 = 52; const DIGIT_5 = 53;
	const DIGIT_6 = 54; const DIGIT_7 = 55; const DIGIT_8 = 56; const DIGIT_9 = 57; 
	const DOLLAR = 36; const UNDERSCORE = 95; const SINGLE_QUOTE = 39; const DOUBLE_QUOTE = 34;
	const FORWARD_SLASH = 47; const BACKSLASH = 92; const ASTERIX = 42; const EQUAL = 61; const CARET = 94;
	const COLON = 58; const QUESTION_MARK = 63; const COMMA = 44; const PERIOD = 46; const SEMI_COLON = 59;
	const EXCLAMATION_MARK = 33; const TILDE = 126; const PLUS = 43; const MINUS = 45;
	const AMPERSAND = 38; const PIPE = 124; const GREATER_THAN = 62; const LESS_THAN = 60;
	const PERCENT = 37;
	const NEWLINE = 10; const CARRIAGE_RETURN = 13; const LINE_SEPARATOR = 8232; const PARAGRAPH_SEPARATOR = 8233;	
	
	public static function isValidVariable($c) {
    	if(($c >= self::LOWER_A && $c <= self::LOWER_Z) || ($c >= self::UPPER_A && $c <= self::UPPER_Z) || $c === self::UNDERSCORE || $c === self::DOLLAR) {
    		return true;
    	} else if($c > 0x80) {
    		if($c===170||$c===181||$c===186||($c>=192&&$c<=214)||($c>=216&&$c<=246)||($c>=248&&$c<=705)||($c>=710&&$c<=721)||($c>=736&&$c<=740)||$c===748||$c===750||($c>=880&&$c<=884)||($c>=886&&$c<=887)||($c>=890&&$c<=893)||$c===902||($c>=904&&$c<=906)||$c===908||($c>=910&&$c<=929)||($c>=931&&$c<=1013)||($c>=1015&&$c<=1153)||($c>=1162&&$c<=1319)||($c>=1329&&$c<=1366)||$c===1369||($c>=1377&&$c<=1415)||($c>=1488&&$c<=1514)||($c>=1520&&$c<=1522)||($c>=1568&&$c<=1610)||($c>=1646&&$c<=1647)||($c>=1649&&$c<=1747)||$c===1749||($c>=1765&&$c<=1766)||($c>=1774&&$c<=1775)||($c>=1786&&$c<=1788)||$c===1791||$c===1808||($c>=1810&&$c<=1839)||($c>=1869&&$c<=1957)||$c===1969||($c>=1994&&$c<=2026)||($c>=2036&&$c<=2037)||$c===2042||($c>=2048&&$c<=2069)||$c===2074||$c===2084||$c===2088||($c>=2112&&$c<=2136)||$c===2208||($c>=2210&&$c<=2220)||($c>=2308&&$c<=2361)||$c===2365||$c===2384||($c>=2392&&$c<=2401)||($c>=2417&&$c<=2423)||($c>=2425&&$c<=2431)||($c>=2437&&$c<=2444)||($c>=2447&&$c<=2448)||($c>=2451&&$c<=2472)||($c>=2474&&$c<=2480)||$c===2482||($c>=2486&&$c<=2489)||$c===2493||$c===2510||($c>=2524&&$c<=2525)||($c>=2527&&$c<=2529)||($c>=2544&&$c<=2545)||($c>=2565&&$c<=2570)||($c>=2575&&$c<=2576)||($c>=2579&&$c<=2600)||($c>=2602&&$c<=2608)||($c>=2610&&$c<=2611)||($c>=2613&&$c<=2614)||($c>=2616&&$c<=2617)||($c>=2649&&$c<=2652)||$c===2654||($c>=2674&&$c<=2676)||($c>=2693&&$c<=2701)||($c>=2703&&$c<=2705)||($c>=2707&&$c<=2728)||($c>=2730&&$c<=2736)||($c>=2738&&$c<=2739)||($c>=2741&&$c<=2745)||$c===2749||$c===2768||($c>=2784&&$c<=2785)||($c>=2821&&$c<=2828)||($c>=2831&&$c<=2832)||($c>=2835&&$c<=2856)||($c>=2858&&$c<=2864)||($c>=2866&&$c<=2867)||($c>=2869&&$c<=2873)||$c===2877||($c>=2908&&$c<=2909)||($c>=2911&&$c<=2913)||$c===2929||$c===2947||($c>=2949&&$c<=2954)||($c>=2958&&$c<=2960)||($c>=2962&&$c<=2965)||($c>=2969&&$c<=2970)||$c===2972||($c>=2974&&$c<=2975)||($c>=2979&&$c<=2980)||($c>=2984&&$c<=2986)||($c>=2990&&$c<=3001)||$c===3024||($c>=3077&&$c<=3084)||($c>=3086&&$c<=3088)||($c>=3090&&$c<=3112)||($c>=3114&&$c<=3123)||($c>=3125&&$c<=3129)||$c===3133||($c>=3160&&$c<=3161)||($c>=3168&&$c<=3169)||($c>=3205&&$c<=3212)||($c>=3214&&$c<=3216)||($c>=3218&&$c<=3240)||($c>=3242&&$c<=3251)||($c>=3253&&$c<=3257)||$c===3261||$c===3294||($c>=3296&&$c<=3297)||($c>=3313&&$c<=3314)||($c>=3333&&$c<=3340)||($c>=3342&&$c<=3344)||($c>=3346&&$c<=3386)||$c===3389||$c===3406||($c>=3424&&$c<=3425)||($c>=3450&&$c<=3455)||($c>=3461&&$c<=3478)||($c>=3482&&$c<=3505)||($c>=3507&&$c<=3515)||$c===3517||($c>=3520&&$c<=3526)||($c>=3585&&$c<=3632)||($c>=3634&&$c<=3635)||($c>=3648&&$c<=3654)||($c>=3713&&$c<=3714)||$c===3716||($c>=3719&&$c<=3720)||$c===3722||$c===3725||($c>=3732&&$c<=3735)||($c>=3737&&$c<=3743)||($c>=3745&&$c<=3747)||$c===3749||$c===3751||($c>=3754&&$c<=3755)||($c>=3757&&$c<=3760)||($c>=3762&&$c<=3763)||$c===3773||($c>=3776&&$c<=3780)||$c===3782||($c>=3804&&$c<=3807)||$c===3840||($c>=3904&&$c<=3911)||($c>=3913&&$c<=3948)||($c>=3976&&$c<=3980)||($c>=4096&&$c<=4138)||$c===4159||($c>=4176&&$c<=4181)||($c>=4186&&$c<=4189)||$c===4193||($c>=4197&&$c<=4198)||($c>=4206&&$c<=4208)||($c>=4213&&$c<=4225)||$c===4238||($c>=4256&&$c<=4293)||$c===4295||$c===4301||($c>=4304&&$c<=4346)||($c>=4348&&$c<=4680)||($c>=4682&&$c<=4685)||($c>=4688&&$c<=4694)||$c===4696||($c>=4698&&$c<=4701)||($c>=4704&&$c<=4744)||($c>=4746&&$c<=4749)||($c>=4752&&$c<=4784)||($c>=4786&&$c<=4789)||($c>=4792&&$c<=4798)||$c===4800||($c>=4802&&$c<=4805)||($c>=4808&&$c<=4822)||($c>=4824&&$c<=4880)||($c>=4882&&$c<=4885)||($c>=4888&&$c<=4954)||($c>=4992&&$c<=5007)||($c>=5024&&$c<=5108)||($c>=5121&&$c<=5740)||($c>=5743&&$c<=5759)||($c>=5761&&$c<=5786)||($c>=5792&&$c<=5866)||($c>=5870&&$c<=5872)||($c>=5888&&$c<=5900)||($c>=5902&&$c<=5905)||($c>=5920&&$c<=5937)||($c>=5952&&$c<=5969)||($c>=5984&&$c<=5996)||($c>=5998&&$c<=6000)||($c>=6016&&$c<=6067)||$c===6103||$c===6108||($c>=6176&&$c<=6263)||($c>=6272&&$c<=6312)||$c===6314||($c>=6320&&$c<=6389)||($c>=6400&&$c<=6428)||($c>=6480&&$c<=6509)||($c>=6512&&$c<=6516)||($c>=6528&&$c<=6571)||($c>=6593&&$c<=6599)||($c>=6656&&$c<=6678)||($c>=6688&&$c<=6740)||$c===6823||($c>=6917&&$c<=6963)||($c>=6981&&$c<=6987)||($c>=7043&&$c<=7072)||($c>=7086&&$c<=7087)||($c>=7098&&$c<=7141)||($c>=7168&&$c<=7203)||($c>=7245&&$c<=7247)||($c>=7258&&$c<=7293)||($c>=7401&&$c<=7404)||($c>=7406&&$c<=7409)||($c>=7413&&$c<=7414)||($c>=7424&&$c<=7615)||($c>=7680&&$c<=7957)||($c>=7960&&$c<=7965)||($c>=7968&&$c<=8005)||($c>=8008&&$c<=8013)||($c>=8016&&$c<=8023)||$c===8025||$c===8027||$c===8029||($c>=8031&&$c<=8061)||($c>=8064&&$c<=8116)||($c>=8118&&$c<=8124)||$c===8126||($c>=8130&&$c<=8132)||($c>=8134&&$c<=8140)||($c>=8144&&$c<=8147)||($c>=8150&&$c<=8155)||($c>=8160&&$c<=8172)||($c>=8178&&$c<=8180)||($c>=8182&&$c<=8188)||$c===8305||$c===8319||($c>=8336&&$c<=8348)||$c===8450||$c===8455||($c>=8458&&$c<=8467)||$c===8469||($c>=8473&&$c<=8477)||$c===8484||$c===8486||$c===8488||($c>=8490&&$c<=8493)||($c>=8495&&$c<=8505)||($c>=8508&&$c<=8511)||($c>=8517&&$c<=8521)||$c===8526||($c>=8544&&$c<=8584)||($c>=11264&&$c<=11310)||($c>=11312&&$c<=11358)||($c>=11360&&$c<=11492)||($c>=11499&&$c<=11502)||($c>=11506&&$c<=11507)||($c>=11520&&$c<=11557)||$c===11559||$c===11565||($c>=11568&&$c<=11623)||$c===11631||($c>=11648&&$c<=11670)||($c>=11680&&$c<=11686)||($c>=11688&&$c<=11694)||($c>=11696&&$c<=11702)||($c>=11704&&$c<=11710)||($c>=11712&&$c<=11718)||($c>=11720&&$c<=11726)||($c>=11728&&$c<=11734)||($c>=11736&&$c<=11742)||$c===11823||($c>=12293&&$c<=12295)||($c>=12321&&$c<=12329)||($c>=12337&&$c<=12341)||($c>=12344&&$c<=12348)||($c>=12353&&$c<=12438)||($c>=12445&&$c<=12447)||($c>=12449&&$c<=12538)||($c>=12540&&$c<=12543)||($c>=12549&&$c<=12589)||($c>=12593&&$c<=12686)||($c>=12704&&$c<=12730)||($c>=12784&&$c<=12799)||($c>=13312&&$c<=19893)||($c>=19968&&$c<=40908)||($c>=40960&&$c<=42124)||($c>=42192&&$c<=42237)||($c>=42240&&$c<=42508)||($c>=42512&&$c<=42527)||($c>=42538&&$c<=42539)||($c>=42560&&$c<=42606)||($c>=42623&&$c<=42647)||($c>=42656&&$c<=42735)||($c>=42775&&$c<=42783)||($c>=42786&&$c<=42888)||($c>=42891&&$c<=42894)||($c>=42896&&$c<=42899)||($c>=42912&&$c<=42922)||($c>=43000&&$c<=43009)||($c>=43011&&$c<=43013)||($c>=43015&&$c<=43018)||($c>=43020&&$c<=43042)||($c>=43072&&$c<=43123)||($c>=43138&&$c<=43187)||($c>=43250&&$c<=43255)||$c===43259||($c>=43274&&$c<=43301)||($c>=43312&&$c<=43334)||($c>=43360&&$c<=43388)||($c>=43396&&$c<=43442)||$c===43471||($c>=43520&&$c<=43560)||($c>=43584&&$c<=43586)||($c>=43588&&$c<=43595)||($c>=43616&&$c<=43638)||$c===43642||($c>=43648&&$c<=43695)||$c===43697||($c>=43701&&$c<=43702)||($c>=43705&&$c<=43709)||$c===43712||$c===43714||($c>=43739&&$c<=43741)||($c>=43744&&$c<=43754)||($c>=43762&&$c<=43764)||($c>=43777&&$c<=43782)||($c>=43785&&$c<=43790)||($c>=43793&&$c<=43798)||($c>=43808&&$c<=43814)||($c>=43816&&$c<=43822)||($c>=43968&&$c<=44002)||($c>=44032&&$c<=55203)||($c>=55216&&$c<=55238)||($c>=55243&&$c<=55291)||($c>=63744&&$c<=64109)||($c>=64112&&$c<=64217)||($c>=64256&&$c<=64262)||($c>=64275&&$c<=64279)||$c===64285||($c>=64287&&$c<=64296)||($c>=64298&&$c<=64310)||($c>=64312&&$c<=64316)||$c===64318||($c>=64320&&$c<=64321)||($c>=64323&&$c<=64324)||($c>=64326&&$c<=64433)||($c>=64467&&$c<=64829)||($c>=64848&&$c<=64911)||($c>=64914&&$c<=64967)||($c>=65008&&$c<=65019)||($c>=65136&&$c<=65140)||($c>=65142&&$c<=65276)||($c>=65313&&$c<=65338)||($c>=65345&&$c<=65370)||($c>=65382&&$c<=65470)||($c>=65474&&$c<=65479)||($c>=65482&&$c<=65487)||($c>=65490&&$c<=65495)||($c>=65498&&$c<=65500)) {
    			return true;
    		} else {
    			return false;
    		}
    	} else {
    		return false;
    	}
    }
	public static function isValidVariablePart($c) {
    	if(($c >= self::LOWER_A && $c <= self::LOWER_Z) || ($c >= self::DIGIT_0 && $c <= self::DIGIT_9) || ($c >= self::UPPER_A && $c <= self::UPPER_Z) || $c === self::UNDERSCORE || $c === self::DOLLAR) {
    		return true;
    	} else if($c > 0x80) {
    		if($c===170||$c===181||$c===186||($c>=192&&$c<=214)||($c>=216&&$c<=246)||($c>=248&&$c<=705)||($c>=710&&$c<=721)||($c>=736&&$c<=740)||$c===748||$c===750||($c>=768&&$c<=884)||($c>=886&&$c<=887)||($c>=890&&$c<=893)||$c===902||($c>=904&&$c<=906)||$c===908||($c>=910&&$c<=929)||($c>=931&&$c<=1013)||($c>=1015&&$c<=1153)||($c>=1155&&$c<=1159)||($c>=1162&&$c<=1319)||($c>=1329&&$c<=1366)||$c===1369||($c>=1377&&$c<=1415)||($c>=1425&&$c<=1469)||$c===1471||($c>=1473&&$c<=1474)||($c>=1476&&$c<=1477)||$c===1479||($c>=1488&&$c<=1514)||($c>=1520&&$c<=1522)||($c>=1552&&$c<=1562)||($c>=1568&&$c<=1641)||($c>=1646&&$c<=1747)||($c>=1749&&$c<=1756)||($c>=1759&&$c<=1768)||($c>=1770&&$c<=1788)||$c===1791||($c>=1808&&$c<=1866)||($c>=1869&&$c<=1969)||($c>=1984&&$c<=2037)||$c===2042||($c>=2048&&$c<=2093)||($c>=2112&&$c<=2139)||$c===2208||($c>=2210&&$c<=2220)||($c>=2276&&$c<=2302)||($c>=2304&&$c<=2403)||($c>=2406&&$c<=2415)||($c>=2417&&$c<=2423)||($c>=2425&&$c<=2431)||($c>=2433&&$c<=2435)||($c>=2437&&$c<=2444)||($c>=2447&&$c<=2448)||($c>=2451&&$c<=2472)||($c>=2474&&$c<=2480)||$c===2482||($c>=2486&&$c<=2489)||($c>=2492&&$c<=2500)||($c>=2503&&$c<=2504)||($c>=2507&&$c<=2510)||$c===2519||($c>=2524&&$c<=2525)||($c>=2527&&$c<=2531)||($c>=2534&&$c<=2545)||($c>=2561&&$c<=2563)||($c>=2565&&$c<=2570)||($c>=2575&&$c<=2576)||($c>=2579&&$c<=2600)||($c>=2602&&$c<=2608)||($c>=2610&&$c<=2611)||($c>=2613&&$c<=2614)||($c>=2616&&$c<=2617)||$c===2620||($c>=2622&&$c<=2626)||($c>=2631&&$c<=2632)||($c>=2635&&$c<=2637)||$c===2641||($c>=2649&&$c<=2652)||$c===2654||($c>=2662&&$c<=2677)||($c>=2689&&$c<=2691)||($c>=2693&&$c<=2701)||($c>=2703&&$c<=2705)||($c>=2707&&$c<=2728)||($c>=2730&&$c<=2736)||($c>=2738&&$c<=2739)||($c>=2741&&$c<=2745)||($c>=2748&&$c<=2757)||($c>=2759&&$c<=2761)||($c>=2763&&$c<=2765)||$c===2768||($c>=2784&&$c<=2787)||($c>=2790&&$c<=2799)||($c>=2817&&$c<=2819)||($c>=2821&&$c<=2828)||($c>=2831&&$c<=2832)||($c>=2835&&$c<=2856)||($c>=2858&&$c<=2864)||($c>=2866&&$c<=2867)||($c>=2869&&$c<=2873)||($c>=2876&&$c<=2884)||($c>=2887&&$c<=2888)||($c>=2891&&$c<=2893)||($c>=2902&&$c<=2903)||($c>=2908&&$c<=2909)||($c>=2911&&$c<=2915)||($c>=2918&&$c<=2927)||$c===2929||($c>=2946&&$c<=2947)||($c>=2949&&$c<=2954)||($c>=2958&&$c<=2960)||($c>=2962&&$c<=2965)||($c>=2969&&$c<=2970)||$c===2972||($c>=2974&&$c<=2975)||($c>=2979&&$c<=2980)||($c>=2984&&$c<=2986)||($c>=2990&&$c<=3001)||($c>=3006&&$c<=3010)||($c>=3014&&$c<=3016)||($c>=3018&&$c<=3021)||$c===3024||$c===3031||($c>=3046&&$c<=3055)||($c>=3073&&$c<=3075)||($c>=3077&&$c<=3084)||($c>=3086&&$c<=3088)||($c>=3090&&$c<=3112)||($c>=3114&&$c<=3123)||($c>=3125&&$c<=3129)||($c>=3133&&$c<=3140)||($c>=3142&&$c<=3144)||($c>=3146&&$c<=3149)||($c>=3157&&$c<=3158)||($c>=3160&&$c<=3161)||($c>=3168&&$c<=3171)||($c>=3174&&$c<=3183)||($c>=3202&&$c<=3203)||($c>=3205&&$c<=3212)||($c>=3214&&$c<=3216)||($c>=3218&&$c<=3240)||($c>=3242&&$c<=3251)||($c>=3253&&$c<=3257)||($c>=3260&&$c<=3268)||($c>=3270&&$c<=3272)||($c>=3274&&$c<=3277)||($c>=3285&&$c<=3286)||$c===3294||($c>=3296&&$c<=3299)||($c>=3302&&$c<=3311)||($c>=3313&&$c<=3314)||($c>=3330&&$c<=3331)||($c>=3333&&$c<=3340)||($c>=3342&&$c<=3344)||($c>=3346&&$c<=3386)||($c>=3389&&$c<=3396)||($c>=3398&&$c<=3400)||($c>=3402&&$c<=3406)||$c===3415||($c>=3424&&$c<=3427)||($c>=3430&&$c<=3439)||($c>=3450&&$c<=3455)||($c>=3458&&$c<=3459)||($c>=3461&&$c<=3478)||($c>=3482&&$c<=3505)||($c>=3507&&$c<=3515)||$c===3517||($c>=3520&&$c<=3526)||$c===3530||($c>=3535&&$c<=3540)||$c===3542||($c>=3544&&$c<=3551)||($c>=3570&&$c<=3571)||($c>=3585&&$c<=3642)||($c>=3648&&$c<=3662)||($c>=3664&&$c<=3673)||($c>=3713&&$c<=3714)||$c===3716||($c>=3719&&$c<=3720)||$c===3722||$c===3725||($c>=3732&&$c<=3735)||($c>=3737&&$c<=3743)||($c>=3745&&$c<=3747)||$c===3749||$c===3751||($c>=3754&&$c<=3755)||($c>=3757&&$c<=3769)||($c>=3771&&$c<=3773)||($c>=3776&&$c<=3780)||$c===3782||($c>=3784&&$c<=3789)||($c>=3792&&$c<=3801)||($c>=3804&&$c<=3807)||$c===3840||($c>=3864&&$c<=3865)||($c>=3872&&$c<=3881)||$c===3893||$c===3895||$c===3897||($c>=3902&&$c<=3911)||($c>=3913&&$c<=3948)||($c>=3953&&$c<=3972)||($c>=3974&&$c<=3991)||($c>=3993&&$c<=4028)||$c===4038||($c>=4096&&$c<=4169)||($c>=4176&&$c<=4253)||($c>=4256&&$c<=4293)||$c===4295||$c===4301||($c>=4304&&$c<=4346)||($c>=4348&&$c<=4680)||($c>=4682&&$c<=4685)||($c>=4688&&$c<=4694)||$c===4696||($c>=4698&&$c<=4701)||($c>=4704&&$c<=4744)||($c>=4746&&$c<=4749)||($c>=4752&&$c<=4784)||($c>=4786&&$c<=4789)||($c>=4792&&$c<=4798)||$c===4800||($c>=4802&&$c<=4805)||($c>=4808&&$c<=4822)||($c>=4824&&$c<=4880)||($c>=4882&&$c<=4885)||($c>=4888&&$c<=4954)||($c>=4957&&$c<=4959)||($c>=4992&&$c<=5007)||($c>=5024&&$c<=5108)||($c>=5121&&$c<=5740)||($c>=5743&&$c<=5759)||($c>=5761&&$c<=5786)||($c>=5792&&$c<=5866)||($c>=5870&&$c<=5872)||($c>=5888&&$c<=5900)||($c>=5902&&$c<=5908)||($c>=5920&&$c<=5940)||($c>=5952&&$c<=5971)||($c>=5984&&$c<=5996)||($c>=5998&&$c<=6000)||($c>=6002&&$c<=6003)||($c>=6016&&$c<=6099)||$c===6103||($c>=6108&&$c<=6109)||($c>=6112&&$c<=6121)||($c>=6155&&$c<=6157)||($c>=6160&&$c<=6169)||($c>=6176&&$c<=6263)||($c>=6272&&$c<=6314)||($c>=6320&&$c<=6389)||($c>=6400&&$c<=6428)||($c>=6432&&$c<=6443)||($c>=6448&&$c<=6459)||($c>=6470&&$c<=6509)||($c>=6512&&$c<=6516)||($c>=6528&&$c<=6571)||($c>=6576&&$c<=6601)||($c>=6608&&$c<=6617)||($c>=6656&&$c<=6683)||($c>=6688&&$c<=6750)||($c>=6752&&$c<=6780)||($c>=6783&&$c<=6793)||($c>=6800&&$c<=6809)||$c===6823||($c>=6912&&$c<=6987)||($c>=6992&&$c<=7001)||($c>=7019&&$c<=7027)||($c>=7040&&$c<=7155)||($c>=7168&&$c<=7223)||($c>=7232&&$c<=7241)||($c>=7245&&$c<=7293)||($c>=7376&&$c<=7378)||($c>=7380&&$c<=7414)||($c>=7424&&$c<=7654)||($c>=7676&&$c<=7957)||($c>=7960&&$c<=7965)||($c>=7968&&$c<=8005)||($c>=8008&&$c<=8013)||($c>=8016&&$c<=8023)||$c===8025||$c===8027||$c===8029||($c>=8031&&$c<=8061)||($c>=8064&&$c<=8116)||($c>=8118&&$c<=8124)||$c===8126||($c>=8130&&$c<=8132)||($c>=8134&&$c<=8140)||($c>=8144&&$c<=8147)||($c>=8150&&$c<=8155)||($c>=8160&&$c<=8172)||($c>=8178&&$c<=8180)||($c>=8182&&$c<=8188)||($c>=8204&&$c<=8205)||($c>=8255&&$c<=8256)||$c===8276||$c===8305||$c===8319||($c>=8336&&$c<=8348)||($c>=8400&&$c<=8412)||$c===8417||($c>=8421&&$c<=8432)||$c===8450||$c===8455||($c>=8458&&$c<=8467)||$c===8469||($c>=8473&&$c<=8477)||$c===8484||$c===8486||$c===8488||($c>=8490&&$c<=8493)||($c>=8495&&$c<=8505)||($c>=8508&&$c<=8511)||($c>=8517&&$c<=8521)||$c===8526||($c>=8544&&$c<=8584)||($c>=11264&&$c<=11310)||($c>=11312&&$c<=11358)||($c>=11360&&$c<=11492)||($c>=11499&&$c<=11507)||($c>=11520&&$c<=11557)||$c===11559||$c===11565||($c>=11568&&$c<=11623)||$c===11631||($c>=11647&&$c<=11670)||($c>=11680&&$c<=11686)||($c>=11688&&$c<=11694)||($c>=11696&&$c<=11702)||($c>=11704&&$c<=11710)||($c>=11712&&$c<=11718)||($c>=11720&&$c<=11726)||($c>=11728&&$c<=11734)||($c>=11736&&$c<=11742)||($c>=11744&&$c<=11775)||$c===11823||($c>=12293&&$c<=12295)||($c>=12321&&$c<=12335)||($c>=12337&&$c<=12341)||($c>=12344&&$c<=12348)||($c>=12353&&$c<=12438)||($c>=12441&&$c<=12442)||($c>=12445&&$c<=12447)||($c>=12449&&$c<=12538)||($c>=12540&&$c<=12543)||($c>=12549&&$c<=12589)||($c>=12593&&$c<=12686)||($c>=12704&&$c<=12730)||($c>=12784&&$c<=12799)||($c>=13312&&$c<=19893)||($c>=19968&&$c<=40908)||($c>=40960&&$c<=42124)||($c>=42192&&$c<=42237)||($c>=42240&&$c<=42508)||($c>=42512&&$c<=42539)||($c>=42560&&$c<=42607)||($c>=42612&&$c<=42621)||($c>=42623&&$c<=42647)||($c>=42655&&$c<=42737)||($c>=42775&&$c<=42783)||($c>=42786&&$c<=42888)||($c>=42891&&$c<=42894)||($c>=42896&&$c<=42899)||($c>=42912&&$c<=42922)||($c>=43000&&$c<=43047)||($c>=43072&&$c<=43123)||($c>=43136&&$c<=43204)||($c>=43216&&$c<=43225)||($c>=43232&&$c<=43255)||$c===43259||($c>=43264&&$c<=43309)||($c>=43312&&$c<=43347)||($c>=43360&&$c<=43388)||($c>=43392&&$c<=43456)||($c>=43471&&$c<=43481)||($c>=43520&&$c<=43574)||($c>=43584&&$c<=43597)||($c>=43600&&$c<=43609)||($c>=43616&&$c<=43638)||($c>=43642&&$c<=43643)||($c>=43648&&$c<=43714)||($c>=43739&&$c<=43741)||($c>=43744&&$c<=43759)||($c>=43762&&$c<=43766)||($c>=43777&&$c<=43782)||($c>=43785&&$c<=43790)||($c>=43793&&$c<=43798)||($c>=43808&&$c<=43814)||($c>=43816&&$c<=43822)||($c>=43968&&$c<=44010)||($c>=44012&&$c<=44013)||($c>=44016&&$c<=44025)||($c>=44032&&$c<=55203)||($c>=55216&&$c<=55238)||($c>=55243&&$c<=55291)||($c>=63744&&$c<=64109)||($c>=64112&&$c<=64217)||($c>=64256&&$c<=64262)||($c>=64275&&$c<=64279)||($c>=64285&&$c<=64296)||($c>=64298&&$c<=64310)||($c>=64312&&$c<=64316)||$c===64318||($c>=64320&&$c<=64321)||($c>=64323&&$c<=64324)||($c>=64326&&$c<=64433)||($c>=64467&&$c<=64829)||($c>=64848&&$c<=64911)||($c>=64914&&$c<=64967)||($c>=65008&&$c<=65019)||($c>=65024&&$c<=65039)||($c>=65056&&$c<=65062)||($c>=65075&&$c<=65076)||($c>=65101&&$c<=65103)||($c>=65136&&$c<=65140)||($c>=65142&&$c<=65276)||($c>=65296&&$c<=65305)||($c>=65313&&$c<=65338)||$c===65343||($c>=65345&&$c<=65370)||($c>=65382&&$c<=65470)||($c>=65474&&$c<=65479)||($c>=65482&&$c<=65487)||($c>=65490&&$c<=65495)||($c>=65498&&$c<=65500)) {
    			return true;
    		} else {
    			return false;
    		}
    	} else {
    		return false;
    	}
    }						
	public function minify($code) {
		return $this->parse($code, array('minify' => 1));
	}	
	public function isValid() {
		return $this->valid;
	}
	public function rewrite($code) {
		return $this->parse($code, array('minify' => 1, 'rewrite' => 1));
	}
	public function getParseTree($code) {
		$this->parse($code, array('parseTree'=>1, 'minify' => 1));
		return '<parseTree>'.$this->parseTree.'</parseTree>';
	}
	protected function asi($useOutput = false) {
        if($this->isFor[$this->lookupSquare.''.$this->lookupCurly.''.($this->lookupParen-1)] && !$this->isForIn[$this->lookupSquare.''.$this->lookupCurly.''.($this->lookupParen-1)]) {
            $this->lastState = 'ForSemi';
            if($useOutput) { 
                $this->output .=  ';';
            } else {
                $this->outputLine .=  ';';
            }
            if($this->isFor[$this->lookupSquare.''.$this->lookupCurly.''.($this->lookupParen-1)] > 2) {
                $this->error('Syntax error unexpected for semi ;');
            }
            $this->isFor[$this->lookupSquare.''.$this->lookupCurly.''.($this->lookupParen-1)]++;
            $this->isVar[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 0;                             
          } else { 
            if($useOutput) {                                                                                      
               $this->output .=  ';';
            } else {
               $this->outputLine = ';' . $this->outputLine;
            }
            $this->lastState = 'EndStatement';
            $this->left = 0;
            $this->isVar[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 0;                               
          }
    }
	protected function error($str) {			
		 throw new Exception($str. '. At position ' . $this->pos . ' - ' . substr($this->code, -10));
	} 
	protected function charCodeAt($str, $i){
		if(!$this->mb) {
			return ord(substr($str, $i, 1));
		}
		$c = mb_substr($str,$i);
		if(empty($c)) {
			return false;
		}								  
		return $this->uniord($c);
	}
	protected function charAt($str, $i) {		
		if(!$this->mb) {
			$c = substr($str, $i, 1);
		} else {	
			$c = mb_substr($str, $i, 1);
		}
		if(empty($c)) {
			return false;
		} else {
			return $c;
		}
	}	
	protected function uniord($c) {
        $h = ord($c{0});
        if ($h <= 0x7F) {
            return $h;
        } else if ($h < 0xC2) {
            return false;
        } else if ($h <= 0xDF) {
            return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
        } else if ($h <= 0xEF) {
            return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
                                     | (ord($c{2}) & 0x3F);
        } else if ($h <= 0xF4) {
            return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
                                     | (ord($c{2}) & 0x3F) << 6
                                     | (ord($c{3}) & 0x3F);
        } else {
            return false;
        }
    }	
	public function parse($code, $options = array()) {									
		$this->valid = false;
		$this->code = $code;				
		$rules=array('ArrayComma'=>array('Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'Return'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'ArrayOpen'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1),'ArrayClose'=>array('ArrayComma'=> 1,'ArrayOpen'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'AccessorOpen'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'AccessorClose'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'Addition'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'AdditionAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'AssignmentDivide'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'AndAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'BlockStatementCurlyOpen'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'BlockStatementCurlyClose'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'BitwiseNot'=>array('Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1),'BitwiseOr'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'BitwiseAnd'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'Break'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'Case'=>array('SwitchStatementCurlyOpen'=> 1,'EndStatement'=> 1,'SwitchColon'=> 1),'Default'=>array('SwitchStatementCurlyOpen'=> 1,'EndStatement'=> 1,'SwitchColon'=> 1),'Debugger'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'Delete'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1),'Do'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'DoStatementCurlyOpen'=>array('Do'=> 1),'DoStatementCurlyClose'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'DivideOperator'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'CatchStatement'=>array('TryStatementCurlyClose'=> 1),'CatchStatementParenOpen'=>array('CatchStatement'=> 1),'CatchStatementParenClose'=>array('CatchStatementIdentifier'=> 1),'CatchStatementIdentifier'=>array('CatchStatementParenOpen'=> 1),'CatchStatementCurlyOpen'=>array('CatchStatementParenClose'=> 1),'CatchStatementCurlyClose'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'Comma'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'Continue'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'EqualAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'Equal'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'Else'=>array('IfStatementCurlyClose'=> 1,'Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'ElseCurlyOpen'=>array('Else'=> 1),'ElseCurlyClose'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'EndStatement'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Continue'=> 1,'Break'=> 1),'False'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1),'FinallyStatement'=>array('CatchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1),'FinallyStatementCurlyOpen'=>array('FinallyStatement'=> 1),'FinallyStatementCurlyClose'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'ForStatement'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'ForStatementParenOpen'=>array('ForStatement'=> 1),'ForStatementParenClose'=>array('ForSemi'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'ForStatementCurlyOpen'=>array('ForStatementParenClose'=> 1),'ForStatementCurlyClose'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'ForSemi'=>array('ForSemi'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'ForStatementParenOpen'=> 1),'FunctionCallOpen'=>array('Identifier'=> 1,'FunctionExpressionCurlyClose'=> 1,'ParenExpressionClose'=> 1,'AccessorClose'=> 1,'FunctionCallClose'=> 1,'This'=> 1),'FunctionCallClose'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'FunctionCallOpen'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'FunctionArgumentIdentifier'=>array('FunctionParenOpen'=> 1,'FunctionArgumentComma'=> 1),'FunctionArgumentComma'=>array('FunctionArgumentIdentifier'=> 1),'FunctionIdentifier'=>array('FunctionStatement'=> 1),'FunctionParenOpen'=>array('FunctionIdentifier'=> 1),'FunctionExpression'=>array('In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'Return'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1),'FunctionExpressionIdentifier'=>array('FunctionExpression'=> 1),'FunctionExpressionParenOpen'=>array('FunctionExpression'=> 1,'FunctionExpressionIdentifier'=> 1),'FunctionExpressionArgumentIdentifier'=>array('FunctionExpressionParenOpen'=> 1,'FunctionExpressionArgumentComma'=> 1),'FunctionExpressionArgumentComma'=>array('FunctionExpressionArgumentIdentifier'=> 1),'FunctionParenClose'=>array('FunctionParenOpen'=> 1,'FunctionArgumentIdentifier'=> 1),'FunctionExpressionParenClose'=>array('FunctionExpressionArgumentIdentifier'=> 1,'FunctionExpressionParenOpen'=> 1),'FunctionExpressionCurlyOpen'=>array('FunctionExpressionParenClose'=> 1),'FunctionStatement'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'FunctionStatementCurlyOpen'=>array('FunctionParenClose'=> 1),'FunctionStatementCurlyClose'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'FunctionExpressionCurlyClose'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'GreaterThan'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'GreaterThanEqual'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'IdentifierDot'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'Identifier'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'IdentifierDot'=> 1),'IfStatement'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'IfStatementParenOpen'=>array('IfStatement'=> 1),'IfStatementParenClose'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'IfStatementCurlyOpen'=>array('IfStatementParenClose'=> 1),'IfStatementCurlyClose'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'In'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'Infinity'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1),'InstanceOf'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'LabelColon'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'LessThan'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'LessThanEqual'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'LeftShift'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'LeftShiftAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'LogicalOr'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'LogicalAnd'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'NaN'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1),'New'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1),'Number'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1),'Null'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1),'NotEqual'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'Not'=>array('Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1),'Nothing'=>array(),'Minus'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'MinusAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'Modulus'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'ModulusAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'Multiply'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'MultiplyAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'ObjectLiteralCurlyOpen'=>array('Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'Return'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1),'ObjectLiteralCurlyClose'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'ObjectLiteralCurlyOpen'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'ObjectLiteralIdentifier'=>array('ObjectLiteralCurlyOpen'=> 1,'ObjectLiteralComma'=> 1),'ObjectLiteralColon'=>array('ObjectLiteralIdentifier'=> 1,'ObjectLiteralIdentifierNumber'=> 1,'ObjectLiteralIdentifierString'=> 1),'ObjectLiteralComma'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'ObjectLiteralIdentifierNumber'=>array('ObjectLiteralCurlyOpen'=> 1,'ObjectLiteralComma'=> 1),'ObjectLiteralIdentifierString'=>array('ObjectLiteralCurlyOpen'=> 1,'ObjectLiteralComma'=> 1),'OrAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'ParenExpressionOpen'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1),'ParenExpressionComma'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'ParenExpressionClose'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'$this->postfixIncrement'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'$this->postfixDeincrement'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'PrefixDeincrement'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1),'PrefixIncrement'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1),'Return'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'RegExp'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1),'RightShift'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'RightShiftAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'String'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1),'StrictEqual'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'StrictNotEqual'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'SwitchStatement'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'SwitchStatementParenOpen'=>array('SwitchStatement'=> 1),'SwitchStatementParenClose'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'SwitchStatementCurlyOpen'=>array('SwitchStatementParenClose'=> 1),'SwitchStatementCurlyClose'=>array('SwitchStatementCurlyOpen'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'SwitchColon'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'Default'=> 1),'This'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1),'TernaryQuestionMark'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'TernaryColon'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'TryStatement'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'TryStatementCurlyOpen'=>array('TryStatement'=> 1),'TryStatementCurlyClose'=>array('TryStatementCurlyOpen'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'True'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1),'Throw'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1),'TypeOf'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1),'UnaryPlus'=>array('Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1),'UnaryMinus'=>array('Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1),'Undefined'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1,'Not'=> 1,'BitwiseNot'=> 1,'UnaryMinus'=> 1,'UnaryPlus'=> 1,'PrefixDeincrement'=> 1,'PrefixIncrement'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1),'Var'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1),'VarIdentifier'=>array('Var'=> 1,'VarComma'=> 1),'VarComma'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'Void'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'Comma'=> 1,'ArrayComma'=> 1,'VarComma'=> 1,'ForStatementParenOpen'=> 1,'IfStatementParenOpen'=> 1,'SwitchStatementParenOpen'=> 1,'WithStatementParenOpen'=> 1,'WhileStatementParenOpen'=> 1,'FunctionCallOpen'=> 1,'ParenExpressionOpen'=> 1,'ArrayOpen'=> 1,'AccessorOpen'=> 1,'Case'=> 1,'New'=> 1,'TypeOf'=> 1,'Delete'=> 1,'Void'=> 1,'ObjectLiteralColon'=> 1,'TernaryQuestionMark'=> 1,'TernaryColon'=> 1,'ForSemi'=> 1,'Continue'=> 1,'Break'=> 1,'Throw'=> 1,'In'=> 1,'InstanceOf'=> 1,'Addition'=> 1,'DivideOperator'=> 1,'Equal'=> 1,'NotEqual'=> 1,'StrictEqual'=> 1,'StrictNotEqual'=> 1,'LogicalOr'=> 1,'BitwiseOr'=> 1,'Xor'=> 1,'Modulus'=> 1,'LogicalAnd'=> 1,'BitwiseAnd'=> 1,'ZeroRightShift'=> 1,'RightShift'=> 1,'GreaterThan'=> 1,'GreaterThanEqual'=> 1,'LeftShift'=> 1,'LessThan'=> 1,'LessThanEqual'=> 1,'Multiply'=> 1,'Minus'=> 1,'EqualAssignment'=> 1,'AdditionAssignment'=> 1,'OrAssignment'=> 1,'XorAssignment'=> 1,'ModulusAssignment'=> 1,'AndAssignment'=> 1,'ZeroRightShiftAssignment'=> 1,'RightShiftAssignment'=> 1,'LeftShiftAssignment'=> 1,'MultiplyAssignment'=> 1,'MinusAssignment'=> 1,'AssignmentDivide'=> 1),'WithStatement'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'WithStatementParenOpen'=>array('WithStatement'=> 1),'WithStatementParenClose'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'WithStatementCurlyOpen'=>array('WithStatementParenClose'=> 1),'WithStatementCurlyClose'=>array('WithStatementCurlyOpen'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'WhileStatement'=>array('Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'WhileStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1),'WhileStatementParenOpen'=>array('WhileStatement'=> 1),'WhileStatementParenClose'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'WhileStatementCurlyOpen'=>array('WhileStatementParenClose'=> 1),'WhileStatementCurlyClose'=>array('WhileStatementCurlyOpen'=> 1,'ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'Nothing'=> 1,'EndStatement'=> 1,'BlockStatementCurlyClose'=> 1,'DoStatementCurlyClose'=> 1,'CatchStatementCurlyClose'=> 1,'ElseCurlyClose'=> 1,'FinallyStatementCurlyClose'=> 1,'FunctionStatementCurlyClose'=> 1,'IfStatementCurlyClose'=> 1,'SwitchStatementCurlyClose'=> 1,'TryStatementCurlyClose'=> 1,'WithStatementCurlyClose'=> 1,'WhileStatementCurlyClose'=> 1,'BlockStatementCurlyOpen'=> 1,'DoStatementCurlyOpen'=> 1,'CatchStatementCurlyOpen'=> 1,'ElseCurlyOpen'=> 1,'FinallyStatementCurlyOpen'=> 1,'FunctionStatementCurlyOpen'=> 1,'IfStatementCurlyOpen'=> 1,'SwitchStatementCurlyOpen'=> 1,'TryStatementCurlyOpen'=> 1,'WithStatementCurlyOpen'=> 1,'FunctionExpressionCurlyOpen'=> 1,'ForStatementCurlyOpen'=> 1,'ForStatementCurlyClose'=> 1,'IfStatementParenClose'=> 1,'SwitchStatementParenClose'=> 1,'WithStatementParenClose'=> 1,'WhileStatementParenClose'=> 1,'ForStatementParenClose'=> 1,'LabelColon'=> 1,'Return'=> 1,'Else'=> 1,'SwitchColon'=> 1,'Do'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1,'Break'=> 1,'Continue'=> 1),'Xor'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'XorAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1),'ZeroRightShift'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1,'$this->postfixIncrement'=> 1,'$this->postfixDeincrement'=> 1),'ZeroRightShiftAssignment'=>array('ArrayClose'=> 1,'AccessorClose'=> 1,'False'=> 1,'FunctionCallClose'=> 1,'FunctionExpressionCurlyClose'=> 1,'Identifier'=> 1,'Infinity'=> 1,'NaN'=> 1,'Number'=> 1,'Null'=> 1,'ObjectLiteralCurlyClose'=> 1,'ParenExpressionClose'=> 1,'RegExp'=> 1,'String'=> 1,'This'=> 1,'True'=> 1,'Undefined'=> 1,'VarIdentifier'=> 1));				 
		$this->mb = mb_strlen($code) === strlen($code) ? false : true;					 													
		$this->scoping = '$'; $this->pos = 0; $this->chr = ''; $this->parentState; $this->parentStates = array(); $this->states = array(); 
		$this->msg = ''; $this->state = 'Nothing'; $this->left = 0; $this->output = ''; $this->outputLine = ''; 
		$last = ''; $next = ''; $next2 = ''; $next3 = ''; $next4 = ''; $next5 = ''; $next6 = ''; $next7 = ''; $next8 = ''; $next9 = ''; $next10 = ''; 
		$unicodeChr1 = ''; $unicodeChr2 = ''; $unicodeChr3 = ''; $unicodeChr4 = '';
		$previous = ''; $previous2 = ''; $previous3 = ''; $previous4 = '';$previous5 = ''; $length = mb_strlen($code); $parseTree = (int) $options['parseTree'];		
		$this->lookupSquare = 0; $this->lookupCurly = 0; $this->lookupParen = 0; $this->ternaryCount = 0; $this->isTernary = array(); 
		$this->caseCount = 0; $this->isCase = array(); $this->isVar = array();
		$this->isFor = array(); $this->isForIn = array();  $this->isIf = array(); $this->isObjectLiteral = array(); 
		$expected = 0; $expect = 0; $expected2 = 0; $expected3 = 0; 
		$expected4 = 0; $this->lastState = 'Nothing'; $newLineFlag = 0;
		
		if($length > 0xfff) {
			$this->error("Sorry PHP is too slow to support this amount of javascript :(");
		}
																					       											
		for(;;) {
			$this->outputLine = '';					
			if($this->pos === $length) {						
				break;
			}	
			$this->state = 'Nothing';
			if($expected || $expected2 || $expected3 || $expected4) {
				$expect = 1;
			}									
			$this->chr = $this->charCodeAt($code, $this->pos);
			$next =$this->charCodeAt($code, $this->pos+1);
		    if($this->chr === self::NEWLINE || $this->chr === self::CARRIAGE_RETURN || $this->chr === self::LINE_SEPARATOR || $this->chr === self::PARAGRAPH_SEPARATOR) {                                                   
                $newLineFlag = 1;
                if(!$options['minify']) {
					$this->output .=  $this->charAt($code, $this->pos); 
				}
                $this->pos++;				                            
                if($this->lastState === 'Break' || $this->lastState === 'Continue' || $this->lastState === 'Return') {
                    $this->asi(true);
                }
                continue;   
			} else if($this->chr === 9 || $this->chr === 11 || $this->chr === 12 || $this->chr === 32 || $this->chr === 160 || $this->chr === 5760 || $this->chr === 6158 || $this->chr === 8192 || $this->chr === 8193 || $this->chr === 8194 || $this->chr === 8195 || $this->chr === 8196 || $this->chr === 8197 || $this->chr === 8198 || $this->chr === 8199 || $this->chr === 8200 || $this->chr === 8201 || $this->chr === 8202 || $this->chr === 8239 || $this->chr === 8287 || $this->chr === 12288) {
				if(!$options['minify']) {
					$this->output .=  $this->charAt($code, $this->pos); 
				}	
				$this->pos++;
				continue;																						
			} else if(($this->chr >= self::DIGIT_0 && $this->chr <= self::DIGIT_9) || (!$this->left && $this->chr === self::PERIOD)) {																														
				if($rules['ObjectLiteralIdentifierNumber'][$this->lastState]) {
					$this->state = 'ObjectLiteralIdentifierNumber';
					$expected = 'ObjectLiteralColon';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;							
				} else if($rules['Number'][$this->lastState]) {
					$this->left = 1;
	           	 	$this->state = 'Number';
	          	} else {
	          		if(!$rules['Number'][$this->lastState] && $newLineFlag) {                                                                                    
                        $this->asi();
                        $this->left = 1;                                    
                        $this->state = 'Number';                                
                    }
	          	}
				
				$this->states = array('hex' => 0, 'len' => 0, 'dot' => 0, 'e' => 0);                        
	            if($this->chr === self::DIGIT_0 && ($next === self::UPPER_X || $next === self::LOWER_X)) {
	                $this->states['hex'] = 2;
	                $this->outputLine .=  '0x';
	                $this->pos+=2;
	                $this->states['len'] = 2;                
	            }                  
	            for(;;) {	            		            		            	
	                $this->chr = $this->charCodeAt($code, $this->pos);                
	                $next = $this->charCodeAt($code, $this->pos+1);                
	                if($this->states['len'] === 0 && $this->chr === self::DIGIT_0 && ($next >= self::DIGIT_0 && $next <= self::DIGIT_9)) {
	                   $this->pos++;
	                   continue 1; 
	                } else if($this->chr >= self::DIGIT_0 && $this->chr <= self::DIGIT_9) {				                    
	                	if($this->states['hex']) {
	                		$this->states['hex']++;   
	                	} else if($this->states['e']) {
	                	    $this->states['e']++;
	                	}
	                } else if($this->states['hex'] && (($this->chr >= self::LOWER_A && $this->chr <= self::LOWER_F) || ($this->chr >= self::UPPER_A && $this->chr <= self::UPPER_F))) {
	                	$this->states['hex']++;         	                                    
	                } else if(($this->chr === self::LOWER_E || $this->chr === self::UPPER_E) && $next === self::PLUS && !$this->states['e']) {                                                                
	                    $this->outputLine .=  'e+';
	                    $this->states['e'] = 1;				                    
	                    $this->pos+=2;
	                    continue 1;
	                } else if(!$this->states['hex'] && $this->chr === self::PERIOD && !$this->states['dot']) {                    
	                    if($this->states['e']) {				                        
	                    	break 1;
	                    }
	                    $this->states['dot'] = 1;
	                } else if(!$this->states['hex'] && $this->chr === self::PERIOD && $this->states['dot']) {                	
	                	break 1;
	                } else if(($this->chr === self::LOWER_E || $this->chr === self::UPPER_E) && $next === self::MINUS && !$this->states['e'] && !$this->states['hex']) {
	                	$this->outputLine .=  'e-';
	                	$this->states['e'] = 1;
	                    $this->pos+=2;
	                    continue 1;
	                } else if(($this->chr === self::LOWER_E || $this->chr === self::UPPER_E) && ($next >= self::DIGIT_0 && $next <= self::DIGIT_9 || $next === self::PLUS || $next === self::MINUS) && $this->states['e'] && $this->states['len'] > 0) {                					                					                					                	
	                	break 1;
	                } else if(($this->chr === self::LOWER_E || $this->chr === self::UPPER_E) && $next !== self::MINUS && $next !== self::PLUS && ($next >= self::DIGIT_0 && $next <= self::DIGIT_9)) {
	                	$this->states['e'] = 1;                                
	                } else if(($this->chr === self::LOWER_E || $this->chr === self::UPPER_E) && $next !== self::MINUS && $next !== self::PLUS && (!($next >= self::DIGIT_0 && $next <= self::DIGIT_9))) {
	                	$this->error('Missing exponent');                                                                                                                         
	                } else if(!$this->states['hex'] && (!(($this->chr >= self::DIGIT_0 && $this->chr <= self::DIGIT_9) || $this->chr === self::LOWER_E || $this->chr === self::UPPER_E)) && $this->states['len'] > 0) {                       	                     	                                  
	                    break 1;
	                } else if(!$this->state['hex'] && (!(($this->chr >= self::DIGIT_0 && $this->chr <= self::DIGIT_9) || $this->chr === self::PERIOD || $this->chr === self::LOWER_E || $this->chr === self::UPPER_E)) && $this->states['len'] === 0) {
	                    $this->error('Invalid number');                                                                     
	                } else {				                    				                	
	                	break 1;
	                }
	                                                           
	                $this->outputLine .=  $this->charAt($code, $this->pos);
	                $this->pos++;
	                $this->states['len']++;                
	                if($this->states['complete']) {				                    
	                    break 1;
	                }                
	            }  
	            
	            if($this->chr === self::PERIOD && $this->states['len'] === 1) {
	            	$this->error('Syntax error $expected number');
	            } else if($this->states['hex'] && $this->states['len'] <= 2) {            	            	
	            	$this->error('expected hex digit');
	            } else if($this->states['e'] === 1) {
	                $this->error('expected exponent');
	            }                                                                                                                                                                                    
			} else if(($this->chr >= self::LOWER_A && $this->chr <= self::LOWER_Z) || ($this->chr >= self::UPPER_A && $this->chr <= self::UPPER_Z) || ($this->chr === self::BACKSLASH || $this->isValidVariable($this->chr))) {
				
				$next2 = $this->charCodeAt($code, $this->pos+2);
				$next3 = $this->charCodeAt($code, $this->pos+3);
				$next4 = $this->charCodeAt($code, $this->pos+4);
				$next5 = $this->charCodeAt($code, $this->pos+5);
				$next6 = $this->charCodeAt($code, $this->pos+6);
				$next7 = $this->charCodeAt($code, $this->pos+7);
				$next8 = $this->charCodeAt($code, $this->pos+8);
				$next9 = $this->charCodeAt($code, $this->pos+9);
				$next10 = $this->charCodeAt($code, $this->pos+10);	
				
				//function keyword
				if($this->chr === self::LOWER_F && $next === self::LOWER_U && $next2 === self::LOWER_N && $next3 === self::LOWER_C && $next4 === self::LOWER_T && $next5 === self::LOWER_I && $next6 === self::LOWER_O && $next7 === self::LOWER_N && !$this->isValidVariablePart($next8) && $next8 !== self::BACKSLASH) {								
					if($rules['FunctionExpression'][$this->lastState]) {
						$this->state = 'FunctionExpression';
						$expected = 'FunctionExpressionIdentifier';
						$expected2 = 'FunctionExpressionParenOpen';
						$expected3 = 0;
						$expected4 = 0;
						$expect = 0;																
					} else if($rules['FunctionStatement'][$this->lastState]) {
						$this->state = 'FunctionStatement';
						$expected = 'FunctionIdentifier';
						$expected2 = 0;
						$expected3 = 0;
						$expected4 = 0;
						$expect = 0;
					} else {								    
					    if(!$rules['Identifier'][$this->lastState] && $newLineFlag) {                                                                                    
                            $this->asi();
                            $this->state = 'FunctionStatement';
                            $expected = 'FunctionIdentifier';
                            $expected2 = 0;
                            $expected3 = 0;
                            $expected4 = 0;
                            $expect = 0;
                        } else {
                            $this->error('Unexpected function. Cannot follow '.$this->lastState+'.output:'.$this->output);
                        }                                              
                    }
					$this->left = 0;
					$this->pos+=8;
					$this->outputLine .=  'function';											
				//if keyword
				} else if($this->chr === self::LOWER_I && $next === self::LOWER_F && !$this->isValidVariablePart($next2) && $next2 !== self::BACKSLASH) {
					$this->state = 'IfStatement';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=2;
					if($this->lastState === 'Else') {
						$this->outputLine .=  ' ';
					}
					$this->outputLine .=  'if';
					$this->isIf[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 1;																			
				//var keyword
				} else if($this->chr === self::LOWER_V && $next === self::LOWER_A && $next2 === self::LOWER_R && !$this->isValidVariablePart($next3) && $next3 !== self::BACKSLASH) {																																
					if(!$rules['Var'][$this->lastState]) {                                                                                                                       
                        $this->asi();                                             
                    }
					$this->state = 'Var';
					$expected = 'Identifier';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
					$this->left = 0;	
					$this->pos+=3;	
					$this->outputLine .=  'var ';
					$this->isVar[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 1;																																												
				//for keyword
				} else if($this->chr === self::LOWER_F && $next === self::LOWER_O && $next2 === self::LOWER_R && !$this->isValidVariablePart($next3) && $next3 !== self::BACKSLASH) {
					$this->state = 'ForStatement';
					$expected = 'ForStatementParenOpen';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
					$this->left = 0;	
					$this->pos+=3;	
					$this->outputLine .=  'for ';
					$this->isFor[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 1;																			
				// else keyword
				} else if($this->chr === self::LOWER_E && $next === self::LOWER_L && $next2 === self::LOWER_S && $next3 === self::LOWER_E && !$this->isValidVariablePart($next4) && $next4 !== self::BACKSLASH) {															
					if(!$this->isIf[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen]) {
						$this->error('Syntax error unexpected else');
					}																																						
					$this->state = 'Else';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=4;
					$this->outputLine .=  ' else ';								
				// while keyword
				} else if($this->chr === self::LOWER_W && $next === self::LOWER_H && $next2 === self::LOWER_I && $next3 === self::LOWER_L && $next4 === self::LOWER_E && !$this->isValidVariablePart($next5) && $next5 !== self::BACKSLASH) {
					$this->state = 'WhileStatement';
					$expected = 'WhileStatementParenOpen';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
					$this->left = 0;	
					$this->pos+=5;	
					$this->outputLine .=  'while';							
				// switch keyword
				} else if($this->chr === self::LOWER_S && $next === self::LOWER_W && $next2 === self::LOWER_I && $next3 === self::LOWER_T && $next4 === self::LOWER_C && $next5 === self::LOWER_H && !$this->isValidVariablePart($next6) && $next6 !== self::BACKSLASH) {
					$this->state = 'SwitchStatement';
					$expected = 'SwitchStatementParenOpen';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
					$this->left = 0;	
					$this->pos+=6;	
					$this->outputLine .=  'switch';							
				// with keyword
				} else if($this->chr === self::LOWER_W && $next === self::LOWER_I && $next2 === self::LOWER_T && $next3 === self::LOWER_H && !$this->isValidVariablePart($next4) && $next4 !== self::BACKSLASH) {
					$this->state = 'WithStatement';
					$expected = 'WithStatementParenOpen';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
					$this->left = 0;	
					$this->pos+=4;
					$this->outputLine .=  'with';								
				// this keyword
				} else if($this->chr === self::LOWER_T && $next === self::LOWER_H && $next2 === self::LOWER_I && $next3 === self::LOWER_S && !$this->isValidVariablePart($next4) && $next4 !== self::BACKSLASH) {
					$this->state = 'This';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;	
					$this->pos+=4;
					$this->outputLine .=  'this';								
				// true keyword
				} else if($this->chr === self::LOWER_T && $next === self::LOWER_R && $next2 === self::LOWER_U && $next3 === self::LOWER_E && !$this->isValidVariablePart($next4) && $next4 !== self::BACKSLASH) {
					$this->state = 'True';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;	
					$this->pos+=4;
					$this->outputLine .=  'true';								
				// false keyword
				} else if($this->chr === self::LOWER_F && $next === self::LOWER_A && $next2 === self::LOWER_L && $next3 === self::LOWER_S && $next4 === self::LOWER_E && !$this->isValidVariablePart($next5) && $next5 !== self::BACKSLASH) {
					$this->state = 'False';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;	
					$this->pos+=5;
					$this->outputLine .=  'false';																						
				// NaN keyword
				} else if($this->chr === self::UPPER_N && $next === self::LOWER_A && $next2 === self::UPPER_N && !$this->isValidVariablePart($next3) && $next3 !== self::BACKSLASH) {
					$this->state = 'NaN';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;	
					$this->pos+=3;
					$this->outputLine .=  'NaN';								
				// null keyword
				} else if($this->chr === self::LOWER_N && $next === self::LOWER_U && $next2 === self::LOWER_L && $next3 === self::LOWER_L && !$this->isValidVariablePart($next4) && $next4 !== self::BACKSLASH) {
					$this->state = 'Null';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;	
					$this->pos+=4;
					$this->outputLine .=  'null';								
				// undefined keyword
				} else if($this->lastState !== 'FunctionArgumentComma' && $this->lastState !== 'FunctionExpressionArgumentComma' && $this->chr === self::LOWER_U && $next === self::LOWER_N && $next2 === self::LOWER_D && $next3 === self::LOWER_E && $next4 === self::LOWER_F && $next5 === self::LOWER_I && $next6 === self::LOWER_N && $next7 === self::LOWER_E && $next8 === self::LOWER_D && !$this->isValidVariablePart($next9) && $next9 !== self::BACKSLASH) {
					$this->state = 'Undefined';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;	
					$this->pos+=9;
					$this->outputLine .=  'undefined';								
				// break keyword
				} else if($this->chr === self::LOWER_B && $next === self::LOWER_R && $next2 === self::LOWER_E && $next3 === self::LOWER_A && $next4 === self::LOWER_K && !$this->isValidVariablePart($next5) && $next5 !== self::BACKSLASH) {
					$this->state = 'Break';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=5;
					$this->outputLine .=  'break ';								
				// case keyword
				} else if($this->chr === self::LOWER_C && $next === self::LOWER_A && $next2 === self::LOWER_S && $next3 === self::LOWER_E && !$this->isValidVariablePart($next4) && $next4 !== self::BACKSLASH) {
					$this->state = 'Case';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=4;							
					$this->outputLine .=  'case ';
					$this->isCase[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 1;
					$this->caseCount++;																					
				// catch keyword			
				} else if($this->chr === self::LOWER_C && $next === self::LOWER_A && $next2 === self::LOWER_T && $next3 === self::LOWER_C && $next4 === self::LOWER_H && !$this->isValidVariablePart($next5) && $next5 !== self::BACKSLASH) {
					$this->state = 'CatchStatement';
					$expected = 'CatchStatementParenOpen';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
					$this->left = 0;	
					$this->pos+=5;
					$this->outputLine .=  'catch';										
				// continue keyword			
				} else if($this->chr === self::LOWER_C && $next === self::LOWER_O && $next2 === self::LOWER_N && $next3 === self::LOWER_T && $next4 === self::LOWER_I && $next5 === self::LOWER_N && $next6 === self::LOWER_U && $next7 === self::LOWER_E && !$this->isValidVariablePart($next8) && $next8 !== self::BACKSLASH) {
					$this->state = 'Continue';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=8;
					$this->outputLine .=  'continue ';										
				// debugger keyword			
				} else if($this->chr === self::LOWER_D && $next === self::LOWER_E && $next2 === self::LOWER_B && $next3 === self::LOWER_U && $next4 === self::LOWER_G && $next5 === self::LOWER_G && $next6 === self::LOWER_E && $next7 === self::LOWER_R && !$this->isValidVariablePart($next8) && $next8 !== self::BACKSLASH) {
					$this->state = 'Debugger';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=8;
					$this->outputLine .=  'debugger';										
				// default keyword			
				} else if($this->chr === self::LOWER_D && $next === self::LOWER_E && $next2 === self::LOWER_F && $next3 === self::LOWER_A && $next4 === self::LOWER_U && $next5 === self::LOWER_L && $next6 === self::LOWER_T && !$this->isValidVariablePart($next7) && $next7 !== self::BACKSLASH) {
					$this->state = 'Default';
					$expected = 'SwitchColon';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
					$this->left = 0;	
					$this->pos+=7;
					$this->outputLine .=  'default';										
				// delete keyword			
				} else if($this->chr === self::LOWER_D && $next === self::LOWER_E && $next2 === self::LOWER_L && $next3 === self::LOWER_E && $next4 === self::LOWER_T && $next5 === self::LOWER_E && !$this->isValidVariablePart($next6) && $next6 !== self::BACKSLASH) {
					$this->state = 'Delete';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=6;
					$this->outputLine .=  'delete ';								
				// do keyword		
				} else if($this->chr === self::LOWER_D && $next === self::LOWER_O && !$this->isValidVariablePart($next2) && $next2 !== self::BACKSLASH) {
					$this->state = 'Do';
					$expected = 'DoStatementCurlyOpen';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
					$this->left = 0;	
					$this->pos+=2;
					$this->outputLine .=  'do ';								
				// finally keyword			
				} else if($this->chr === self::LOWER_F && $next === self::LOWER_I && $next2 === self::LOWER_N && $next3 === self::LOWER_A && $next4 === self::LOWER_L && $next5 === self::LOWER_L && $next6 === self::LOWER_Y && !$this->isValidVariablePart($next7) && $next7 !== self::BACKSLASH) {
					$this->state = 'FinallyStatement';
					$expected = 'FinallyStatementCurlyOpen';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
					$this->left = 0;	
					$this->pos+=7;
					$this->outputLine .=  'finally';								
				// in keyword		
				} else if($this->chr === self::LOWER_I && $next === self::LOWER_N && !$this->isValidVariablePart($next2) && $next2 !== self::BACKSLASH) {
					$this->state = 'In';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=2;
					$this->outputLine .=  ' in ';
					if($this->isFor[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen]) {
						$this->isForIn[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 1;		
					}													
				// Infinity keyword		
				} else if($this->chr === self::UPPER_I && $next === self::LOWER_N && $next2 === self::LOWER_F && $next3 === self::LOWER_I && $next4 === self::LOWER_N && $next5 === self::LOWER_I && $next6 === self::LOWER_T && $next7 === self::LOWER_Y && !$this->isValidVariablePart($next8) && $next8 !== self::BACKSLASH) {
					$this->state = 'Infinity';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;	
					$this->pos+=8;
					$this->outputLine .=  'Infinity';																							
				// instanceof keyword		
				} else if($this->chr === self::LOWER_I && $next === self::LOWER_N && $next2 === self::LOWER_S && $next3 === self::LOWER_T && $next4 === self::LOWER_A && $next5 === self::LOWER_N && $next6 === self::LOWER_C && $next7 === self::LOWER_E && $next8 === self::LOWER_O && $next9 === self::LOWER_F && !$this->isValidVariablePart($next10) && $next10 !== self::BACKSLASH) {
					$this->state = 'InstanceOf';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;	
					$this->pos+=10;
					$this->outputLine .=  ' instanceof ';								
				// new keyword
				} else if($this->chr === self::LOWER_N && $next === self::LOWER_E && $next2 === self::LOWER_W && !$this->isValidVariablePart($next3) && $next3 !== self::BACKSLASH) {
					$this->state = 'New';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=3;
					$this->outputLine .=  'new ';									
				// return keyword			
				} else if($this->chr === self::LOWER_R && $next === self::LOWER_E && $next2 === self::LOWER_T && $next3 === self::LOWER_U && $next4 === self::LOWER_R && $next5 === self::LOWER_N && !$this->isValidVariablePart($next6) && $next6 !== self::BACKSLASH) {
					$this->state = 'Return';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=6;
					$this->outputLine .=  'return ';								
				// throw keyword			
				} else if($this->chr === self::LOWER_T && $next === self::LOWER_H && $next2 === self::LOWER_R && $next3 === self::LOWER_O && $next4 === self::LOWER_W && !$this->isValidVariablePart($next5) && $next5 !== self::BACKSLASH) {
					$this->state = 'Throw';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=5;
					$this->outputLine .=  'throw ';								
				// try keyword
				} else if($this->chr === self::LOWER_T && $next === self::LOWER_R && $next2 === self::LOWER_Y && !$this->isValidVariablePart($next3) && $next3 !== self::BACKSLASH) {
					$this->state = 'TryStatement';
					$expected = 'TryStatementCurlyOpen';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
					$this->left = 0;	
					$this->pos+=3;
					$this->outputLine .=  'try';								
				// typeof keyword
				} else if($this->chr === self::LOWER_T && $next === self::LOWER_Y && $next2 === self::LOWER_P && $next3 === self::LOWER_E && $next4 === self::LOWER_O && $next5 === self::LOWER_F && !$this->isValidVariablePart($next6) && $next6 !== self::BACKSLASH) {
					$this->state = 'TypeOf';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=6;
					$this->outputLine .=  'typeof ';																																																				
				// void keyword
				} else if($this->chr === self::LOWER_V && $next === self::LOWER_O && $next2 === self::LOWER_I && $next3 === self::LOWER_D && !$this->isValidVariablePart($next4) && $next4 !== self::BACKSLASH) {
					$this->state = 'Void';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;	
					$this->pos+=4;
					$this->outputLine .=  'void ';
				// prototype keyword
				} else if($this->lastState === 'IdentifierDot' && $this->chr === self::LOWER_P && $next === self::LOWER_R && $next2 === self::LOWER_O && $next3 === self::LOWER_T && $next4 === self::LOWER_O && $next5 === self::LOWER_T && $next6 === self::LOWER_Y && $next7 === self::LOWER_P && $next8 === self::LOWER_E && !$this->isValidVariablePart($next9) && $next9 !== self::BACKSLASH) {																																																									
					$this->state = 'Identifier';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;	
					$this->pos+=9;
					$this->outputLine .=  'prototype';
				// length keyword
				} else if($this->lastState === 'IdentifierDot' && $this->chr === self::LOWER_L && $next === self::LOWER_E && $next2 === self::LOWER_N && $next3 === self::LOWER_G && $next4 === self::LOWER_T && $next5 === self::LOWER_H && !$this->isValidVariablePart($next6) && $next6 !== self::BACKSLASH) {																																																									
					$this->state = 'Identifier';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;	
					$this->pos+=6;
					$this->outputLine .=  'length';
				} else {																																    							   							    				
					// Identifiers																											
					if($rules['FunctionIdentifier'][$this->lastState]) {
						$this->state = 'FunctionIdentifier';
						$expected = 'FunctionParenOpen';
						$expected2 = 0;
						$expected3 = 0;
						$expected4 = 0;
						$expect = 0;
						$this->outputLine .=  ' ';
					} else if($rules['CatchStatementIdentifier'][$this->lastState]) {
						$this->state = 'CatchStatementIdentifier';
						$expected = 'CatchStatementParenClose';
						$expected2 = 0;
						$expected3 = 0;
						$expected4 = 0;
						$expect = 0;
					} else if($rules['ObjectLiteralIdentifier'][$this->lastState]) {
						$this->state = 'ObjectLiteralIdentifier';
						$expected = 'ObjectLiteralColon';
						$expected2 = 0;
						$expected3 = 0;
						$expected4 = 0;
						$expect = 0;									
					} else if($rules['FunctionExpressionIdentifier'][$this->lastState]) {
						$this->state = 'FunctionExpressionIdentifier';
						$expected = 'FunctionExpressionParenOpen';
						$expected2 = 0;
						$expected3 = 0;
						$expected4 = 0;
						$expect = 0;
						$this->outputLine .=  ' ';
					} else if($rules['FunctionArgumentIdentifier'][$this->lastState]) {
						$this->state = 'FunctionArgumentIdentifier';
						$expected = 'FunctionParenClose';
						$expected2 = 'FunctionArgumentComma';
						$expected3 = 0;
						$expected4 = 0;
						$expect = 0;				
					} else if($rules['FunctionExpressionArgumentIdentifier'][$this->lastState]) {
						$this->state = 'FunctionExpressionArgumentIdentifier';
						$expected = 'FunctionExpressionParenClose';
						$expected2 = 'FunctionExpressionArgumentComma';
						$expected3 = 0;
						$expected4 = 0;
						$expect = 0;
					} else if($rules['VarIdentifier'][$this->lastState]) {									
						$this->state = 'VarIdentifier';
						$expected = 0;
						$expected2 = 0;
						$expected3 = 0;
						$expected4 = 0;
						$expect = 0;																													
					} else if($rules['Identifier'][$this->lastState]) {
						$this->state = 'Identifier';
						$expected = 0;
						$expected2 = 0;
						$expected3 = 0;
						$expected4 = 0;
						$this->left = 1;								
					} else {
						if(!$rules['Identifier'][$this->lastState] && $newLineFlag) {                                                                                    
                            $this->asi();                                              
                        }
                        $this->state = 'Identifier';
                        $expected = 0;
                        $expected2 = 0;
                        $expected3 = 0;
                        $expected4 = 0;
                        $this->left = 1;
					}																				
					$this->states = array('first'=>0);
					if($options['rewrite']) {							
						$this->outputLine .=  $this->scoping;
					}
					for(;;) {												
						$this->chr = $this->charCodeAt($code, $this->pos);																		
						if($this->chr === self::BACKSLASH) {
							$next = $this->charCodeAt($code,$this->pos+1);
							$next2 = $this->charCodeAt($code,$this->pos+2);
							$next3 = $this->charCodeAt($code,$this->pos+3);
							$next4 = $this->charCodeAt($code,$this->pos+4);
							$next5 = $this->charCodeAt($code,$this->pos+5);
							$unicodeChr1 = $this->charAt($code, $this->pos+2);
							$unicodeChr2 = $this->charAt($code, $this->pos+3);
							$unicodeChr3 = $this->charAt($code, $this->pos+4);
							$unicodeChr4 = $this->charAt($code, $this->pos+5);																		
							if($next !== self::LOWER_U) {
								$this->error('Invalid unicode escape sequence');
							}
							if(
								(($next2 >= self::LOWER_A && $next2 <= self::LOWER_F) || ($next2 >= self::UPPER_A && $next2 <= self::UPPER_F) || ($next2 >= self::DIGIT_0 && $next2 <= self::DIGIT_9))&&
								(($next3 >= self::LOWER_A && $next3 <= self::LOWER_F) || ($next3 >= self::UPPER_A && $next3 <= self::UPPER_F) || ($next3 >= self::DIGIT_0 && $next3 <= self::DIGIT_9))&&
								(($next4 >= self::LOWER_A && $next4 <= self::LOWER_F) || ($next4 >= self::UPPER_A && $next4 <= self::UPPER_F) || ($next4 >= self::DIGIT_0 && $next4 <= self::DIGIT_9))&&
								(($next5 >= self::LOWER_A && $next5 <= self::LOWER_F) || ($next5 >= self::UPPER_A && $next5 <= self::UPPER_F) || ($next5 >= self::DIGIT_0 && $next5 <= self::DIGIT_9))
							) {
								if(!$this->states['first']) {
									if($this->isValidVariable(hexdec($unicodeChr1.$unicodeChr2.$unicodeChr3.$unicodeChr4))) {
										$this->outputLine .=  '\\u'.$unicodeChr1.$unicodeChr2.$unicodeChr3.$unicodeChr4;
										$this->pos+=6;
										continue 1;
									} else {
										$this->error('Invalid unicode escape sequence used as variable');		
									}
									$this->states['first'] = 1;	
								} else {
									if($this->isValidVariablePart(hexdec($unicodeChr1.$unicodeChr2.$unicodeChr3.$unicodeChr4))) {
										$this->outputLine .=  '\\u'.$unicodeChr1.$unicodeChr2.$unicodeChr3.$unicodeChr4;
										$this->pos+=6;
										continue 1;
									} else {
										$this->error('Invalid unicode escape sequence used as variable');		
									}
								}
							} else {
								$this->error('Invalid hex digits in unicode escape');
							}																														
						} else if(!$this->states['first']) {							
							$this->states['first'] = 1;
							if($this->isValidVariable($this->chr)) {																
							} else {
								$this->error('Unexpected character ' . $this->charAt($code, $this->pos) . '. Cannot follow '.$this->lastState.'.output:'.$this->output);
							}	
						} else {
							if($this->isValidVariablePart($this->chr)) {										
							} else if($this->chr === self::BACKSLASH && !$this->states['unicode']) {
								$this->states['unicode'] = 1;
							} else {
								break 1;
							}
						}															
						$this->outputLine .=  $this->charAt($code, $this->pos);													
						$this->pos++;
					}
					if($options['rewrite']) {
						$this->outputLine .=  $this->scoping;
					}																		
				}
				
				if(!$rules[$this->state][$this->lastState] && $newLineFlag) {                                                                                    
                    $this->asi();                                              
                }																								                                                                                    																																																																			                                                                                                                                                                                                                                                                                     
			} else if($this->chr === self::FORWARD_SLASH) {
				if(!$this->left && $next !== self::ASTERIX && $next !== self::FORWARD_SLASH) {																								
					$this->states = array('escaping'=> 0, 'complete'=> 0, 'open' => 0, 'square'=> 0, 'flags'=> array());       
	                $this->state = 'RegExp';
	                $this->left = 1;               
	                $this->states['open'] = 1; 	                            
	                $this->outputLine .=  '/';		                        
	                $this->pos++;                  
	                while(1) {
	                    $this->chr = $this->charCodeAt($code,$this->pos);
	                    $next = $this->charCodeAt($code,$this->pos+1);                            
	                    if($this->chr === self::FORWARD_SLASH && !$this->states['escaping'] && !$this->states['square']) {
	                        $this->states['open'] = 0;
	                        if($next !== self::LOWER_I && $next !== self::LOWER_M && $next !== self::LOWER_G) {
	                            $this->states['complete'] = 1;
	                        }
	                    } else if($this->chr === self::FORWARD_SLASH && !$this->states['escaping'] && $this->states['square']) {
	                        $this->outputLine .=  '\\';           
	                    } else if($this->chr === self::PAREN_OPEN && !$this->states['escaping'] && $this->states['square']) {
	                    	 $this->outputLine .=  '\\';
						} else if($this->chr === self::PAREN_CLOSE && !$this->states['escaping'] && $this->states['square']) {
	                    	$this->outputLine .=  '\\';                            	    
	                    } else if($this->chr === self::SQUARE_OPEN && !$this->states['escaping'] && $this->states['square']) {                
	                        $this->outputLine .=  '\\';
	                    } else if($this->chr === self::SQUARE_OPEN && !$this->states['escaping'] && !$this->states['square']) {
	                    	$next2 = $this->charCodeAt($code,$this->pos+2); 
	                        if($next === self::SQUARE_CLOSE || ($next === self::CARET && $next2 === self::SQUARE_CLOSE)) {
	                            $this->error('Empty character class not allowed.');
	                        }
	                        $this->states['square'] = 1;               
	                    } else if($this->chr === self::BACKSLASH && !$this->states['escaping']) {
	                        $this->states['escaping'] = 1;
	                    } else if($this->chr === self::BACKSLASH && $this->states['escaping']) {
	                        $this->states['escaping'] = 0;
	                    } else if($this->chr === self::SQUARE_CLOSE && !$this->states['escaping']) {                
	                        $this->states['square'] = 0;               
	                    } else if($this->chr === self::NEWLINE || $this->chr === self::CARRIAGE_RETURN || $this->chr === self::LINE_SEPARATOR || $this->chr === self::PARAGRAPH_SEPARATOR) {
	                        $this->error('Unterminated regex literal');                                
	                    } else if($this->states['escaping']) {
	                        $this->states['escaping'] = 0;
	                    } else if(!$this->states['open'] && $next !== self::LOWER_I && $next !== self::LOWER_M && $next !== self::LOWER_G) {
	                        if(!$this->states['open'] && ($this->chr === self::LOWER_I || $this->chr === self::LOWER_M || $this->chr === self::LOWER_G) && $this->states['flags'][$this->chr]) {
	                            $this->error('Duplicate regex flag');
	                        }               
	                        $this->states['complete'] = 1;
	                    } else if(!$this->states['open'] && ($this->chr === self::LOWER_I || $this->chr === self::LOWER_M || $this->chr === self::LOWER_G) && !$this->states['flags'][$this->chr]) {
	                        $this->states['flags'][$this->chr] = 1;
	                    } 
	                    if($this->pos + 1 > $length && $this->states['open']) {               
	                        $this->error('Unterminated regex literal');
	                    }
	                    
	                    if($this->pos + 1 > $length) { 
	                        break 1;
	                    }	                            
	                    $this->outputLine .=  $this->charAt($code, $this->pos);	                            
	                    $this->pos++;
	                    if($this->states['complete']) {	                                                  
	                        break 1;
	                    }
	                }   
				} else if($next === self::FORWARD_SLASH) {
					$this->states = array();                        
	                $this->pos+=2;
					if($options['comments']) {
						$this->output .=  '//';		
					}                 
	                for(;;) {	                	
	                    $this->chr = $this->charCodeAt($code,$this->pos);						
	                    if($this->chr === self::NEWLINE || $this->chr === self::CARRIAGE_RETURN || $this->chr === self::LINE_SEPARATOR || $this->chr === self::PARAGRAPH_SEPARATOR) {
	                        $this->states['complete'] = 1;
	                    }
	                    if($this->pos + 1 > $length) {
	                        break 1;
	                    }
						if($options['comments']) {
							$this->output .=  $this->charAt($code, $this->pos);
						}	                            
	                    $this->pos++;
	                    if($this->states['complete']) {
	                        break 1;
	                    }
	                }
	                continue;	                           
				} else if($next === self::ASTERIX) {							
	                $this->pos += 2;
					if($options['comments']) {
						$this->output .=  '/*';		
					}                 
	                for(;;) {	                	
	                    $this->chr = $this->charCodeAt($code,$this->pos);
	                    $next = $this->charCodeAt($code,$this->pos+1);                          
	                    if($this->chr === self::ASTERIX && $next === self::FORWARD_SLASH) {
	                        if($options['comments']) {
								$this->output .=  '*/';		
							}	               	                                
	                        $this->pos+=2;
	                        break 1;
	                    }           
	                    if($this->pos + 1 > $length) {             
	                        $this->error('Unterminated multiline comment');
	                    }	
						if($options['comments']) {
							$this->output .=  $this->charAt($code, $this->pos);
						}                            
	                    $this->pos++;	                            
	                } 
	                continue;  
				} else if($this->left && $next !== self::FORWARD_SLASH) {
					$this->left = 0;
					if($next === self::EQUAL) {
						$this->state = 'AssignmentDivide';
						$this->pos+=2;
						$last = self::EQUAL;
						$this->outputLine .=  '/=';	
					} else {
						$this->state = 'DivideOperator';
						$this->pos++;
						$last = self::FORWARD_SLASH;
						$this->outputLine .=  ' / ';	
					}
				} else {
					$this->error('Unexpected /. Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
			} else if($this->chr === self::SQUARE_OPEN) {			
				if(!$this->left) {
					$this->state = 'ArrayOpen';				
				} else {
					$this->state = 'AccessorOpen';																									
				}			
				$this->outputLine .=  '[';
				if($this->state === 'AccessorOpen') {
					if($options['rewrite']) {
						$this->outputLine .=  'M.P(';
					}
				}							
				$this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = $this->state;						
				$this->left = 0;
				$last = self::SQUARE_OPEN;
				$this->pos++;
				$this->lookupSquare++;
				$this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = $this->state;						
			} else if($this->chr === self::SQUARE_CLOSE) {
				$this->lookupSquare--;			
				$this->parentState = $this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen];									
				if($this->parentState === 'ArrayOpen') {
					$this->state = 'ArrayClose';
					$this->left = 1;
				} else if($this->parentState === 'AccessorOpen') {
					$this->state = 'AccessorClose';
					$this->left = 1;
					if($options['rewrite']) {
						$this->outputLine .=  ')';
					}
				} else {				
					$this->error('Unexpected ]. Cannot follow '.$this->lastState.'.output:'.$this->output);
				}													
				$this->outputLine .=  ']';
				$this->left = 1;
				$last = self::SQUARE_CLOSE;
				$this->pos++;
				$this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = '';							
			} else if($this->chr === self::PAREN_OPEN) {																																																																									
				if($this->lastState === 'FunctionIdentifier') {
					$this->state = 'FunctionParenOpen';
					$expect = 0;
					$expected = 'FunctionArgumentIdentifier';
					$expected2 = 'FunctionParenClose';
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'ForStatement') {
					$this->state = 'ForStatementParenOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($rules['FunctionCallOpen'][$this->lastState]) {
					$this->state = 'FunctionCallOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'IfStatement') {
					$this->state = 'IfStatementParenOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;	
				} else if($this->lastState === 'CatchStatement') {
					$this->state = 'CatchStatementParenOpen';
					$expected = 'CatchStatementIdentifier';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;	
					$expect = 0;			
				} else if($this->lastState === 'WhileStatement') {
					$this->state = 'WhileStatementParenOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'SwitchStatement') {
					$this->state = 'SwitchStatementParenOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'WithStatement') {
					$this->state = 'WithStatementParenOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'FunctionExpressionIdentifier') {
					$this->state = 'FunctionExpressionParenOpen';
					$expect = 0;
					$expected = 'FunctionExpressionArgumentIdentifier';
					$expected2 = 'FunctionExpressionParenClose';
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'FunctionExpression') {
					$this->state = 'FunctionExpressionParenOpen';
					$expect = 0;
					$expected = 'FunctionExpressionArgumentIdentifier';
					$expected2 = 'FunctionExpressionParenClose';
					$expected3 = 0;
					$expected4 = 0;							
				} else if($rules['ParenExpressionOpen'][$this->lastState]) {							
					$this->state = 'ParenExpressionOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else {							    
				    if(!$rules['Identifier'][$this->lastState] && $newLineFlag) {                                                                                    
                       $this->asi();
                       $this->state = 'ParenExpressionOpen';
                       $expected = 0;
                       $expected2 = 0;
                       $expected3 = 0;
                       $expected4 = 0;
                    } else {
				       $this->error('Unexpected (. Cannot follow '.$this->lastState.'.output:'.$this->output);
				    }															
				}												
				$this->outputLine .=  '(';
				$last = self::PAREN_OPEN;
				$this->pos++;
				$this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = $this->state;
				$this->left = 0;
				$this->lookupParen++;
			} else if($this->chr === self::PAREN_CLOSE) {
			    $this->isVar[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 0;				
				$this->lookupParen--;						
				$this->parentState = $this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen];																																																					
				if($rules['FunctionParenClose'][$this->lastState]) {
					$this->state = 'FunctionParenClose';
					$expect = 0;
					$expected = 'FunctionStatementCurlyOpen';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->parentState === 'FunctionCallOpen') {
					$this->state = 'FunctionCallClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;
				} else if($this->parentState === 'ForStatementParenOpen') {
					$this->state = 'ForStatementParenClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;
					$this->isFor[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 0;		
					$this->isForIn[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 0;		
				} else if($this->parentState === 'SwitchStatementParenOpen') {
					$this->state = 'SwitchStatementParenClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;
				} else if($this->parentState === 'CatchStatementParenOpen') {
					$this->state = 'CatchStatementParenClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;
				} else if($this->parentState === 'WhileStatementParenOpen') {
					$this->state = 'WhileStatementParenClose';
					$expected = '';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;
				} else if($this->parentState === 'WithStatementParenOpen') {
					$this->state = 'WithStatementParenClose';
					$expected = '';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;
				} else if($this->parentState === 'IfStatementParenOpen') {
					$this->state = 'IfStatementParenClose';
					$expected = '';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;
				} else if($rules['FunctionExpressionParenClose'][$this->lastState]) {
					$this->state = 'FunctionExpressionParenClose';
					$expect = 0;
					$expected = 'FunctionExpressionCurlyOpen';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->parentState === 'ParenExpressionOpen') {
					$this->state = 'ParenExpressionClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;
				} else {																													
					$this->error('Unexpected ). Cannot follow '.$this->lastState.'.output:'.$this->output);							
				}											
				$this->outputLine .=  ')';
				$this->pos++;
				$this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = '';																		
			} else if($this->chr === self::CURLY_OPEN) {																																																
				if($this->lastState === 'FunctionParenClose') {
					$this->state = 'FunctionStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'Do') {
					$this->state = 'DoStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'Else') {
					$this->state = 'ElseCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'WhileStatementParenClose') {
					$this->state = 'WhileStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;	
				} else if($this->lastState === 'CatchStatementParenClose') {
					$this->state = 'CatchStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'ForStatementParenClose') {
					$this->state = 'ForStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'WithStatementParenClose') {
					$this->state = 'WithStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;				
				} else if($this->lastState === 'TryStatement') {
					$this->state = 'TryStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'SwitchStatementParenClose') {
					$this->state = 'SwitchStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'IfStatementParenClose') {
					$this->state = 'IfStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'FinallyStatement') {
					$this->state = 'FinallyStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->lastState === 'FunctionExpressionParenClose') {
					$this->state = 'FunctionExpressionCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($rules['ObjectLiteralCurlyOpen'][$this->lastState]) {				
					$this->state = 'ObjectLiteralCurlyOpen';
					$expected = 'ObjectLiteralIdentifier';
					$expected2 = 'ObjectLiteralIdentifierString';
					$expected3 = 'ObjectLiteralIdentifierNumber';
					$expected4 = 'ObjectLiteralCurlyClose';
					$expect = 0;
					$this->parentStates[$this->lookupSquare.''.($this->lookupCurly+1).''.$this->lookupParen] = $this->state;
					if($options['rewrite']) {
						$this->outputLine .=  'M.O(';
					}
				} else if($rules['BlockStatementCurlyOpen'][$this->lastState]) {
					$this->state = 'BlockStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else {							    							    							  							    
				    if(!$rules['Identifier'][$this->lastState] && $newLineFlag) {							        
				      $this->asi();
				      if($this->lastState === 'ForSemi') {                                    
                        $this->state = 'ObjectLiteralCurlyOpen';
                        $expected = 'ObjectLiteralIdentifier';
                        $expected2 = 'ObjectLiteralIdentifierString';
                        $expected3 = 'ObjectLiteralIdentifierNumber';
                        $expected4 = 'ObjectLiteralCurlyClose';
                        $expect = 0;
                        $this->parentStates[$this->lookupSquare.''.($this->lookupCurly+1).''.$this->lookupParen] = $this->state;
                        $this->outputLine .=  'M.O(';   
				      } else {                                                                                                                             
                        $this->state = 'BlockStatementCurlyOpen';
                        $expected = 0;
                        $expected2 = 0;
                        $expected3 = 0;
                        $expected4 = 0;
                      }                                                                                 
                    } else {												
					    $this->error('Unexpected {. Cannot follow '.$this->lastState.'.output:'.$this->output);
					}
				}										
				$this->outputLine .=  '{';
				if($this->state === 'FunctionStatementCurlyOpen' || $this->state === 'FunctionExpressionCurlyOpen') {
					if($options['rewrite']) {	
						$this->outputLine .=  'var $arguments$=M.A(arguments);';
					}
				}
				$last = self::CURLY_OPEN;
				$this->pos++;
				$this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = $this->state;
				$this->left = 0;
				$this->lookupCurly++;								
			} else if($this->chr === self::CURLY_CLOSE) {							
				$this->isVar[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 0;
				$this->lookupCurly--;																															
				$this->parentState = $this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen];																																													
				$this->outputLine .=  '}';																											
				if($this->parentState === 'FunctionStatementCurlyOpen') {
					$this->state = 'FunctionStatementCurlyClose';								
					$this->left = 0;
				} else if($this->parentState === 'ElseCurlyOpen') {
					$this->state = 'ElseCurlyClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;
				} else if($this->parentState === 'ObjectLiteralCurlyOpen') {
					$this->state = 'ObjectLiteralCurlyClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 1;
					$this->isObjectLiteral[$this->lookupSquare.''.($this->lookupCurly+1).''.$this->lookupParen] = 0;
					if($options['rewrite']) {
						$this->outputLine .=  ')';
					}
				} else if($this->parentState === 'ForStatementCurlyOpen') {
					$this->state = 'ForStatementCurlyClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;								
				} else if($this->parentState === 'WhileStatementCurlyOpen') {
					$this->state = 'WhileStatementCurlyClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;								
				} else if($this->parentState === 'CatchStatementCurlyOpen') {
					$this->state = 'CatchStatementCurlyClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;
				} else if($this->parentState === 'FinallyStatementCurlyOpen') {
					$this->state = 'FinallyStatementCurlyClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;												
				} else if($this->parentState === 'WithStatementCurlyOpen') {
					$this->state = 'WithStatementCurlyClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;								
				} else if($this->parentState === 'TryStatementCurlyOpen') {
					$this->state = 'TryStatementCurlyClose';				
					$expected = 'CatchStatement';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
				} else if($this->parentState === 'DoStatementCurlyOpen') {
					$this->state = 'DoStatementCurlyClose';				
					$expected = 'WhileStatement';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;
				} else if($this->parentState === 'SwitchStatementCurlyOpen') {
					$this->state = 'SwitchStatementCurlyClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;														
				} else if($this->parentState === 'DoStatement') {
					$this->state = 'DoStatementCurlyOpen';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;
					$expect = 0;
				} else if($this->parentState === 'IfStatementCurlyOpen') {
					$this->state = 'IfStatementCurlyClose';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$this->left = 0;
				} else if($this->parentState === 'FunctionExpressionCurlyOpen') {
					$this->state = 'FunctionExpressionCurlyClose';
					$this->left = 1;
				} else if($this->parentState === 'BlockStatementCurlyOpen') {
					$this->state = 'BlockStatementCurlyClose';								
					$this->left = 0;
				} else {																						
					$this->error('Unexpected }. Cannot follow '.$this->lastState.'.output:'.$this->output);
				}							
				$this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = '';										
				$this->pos++;																				
			} else if($this->chr === self::QUESTION_MARK) {
				$this->state = 'TernaryQuestionMark';
				$this->outputLine .=  '?';
				$last = self::QUESTION_MARK;
				$this->left = 0;
				$this->pos++;
				if($this->isTernary[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen]) {
				  $this->isTernary[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen]++;
				} else {
				  $this->isTernary[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 1;
				}
				$this->ternaryCount++;													
			} else if($this->chr === self::COMMA) {			
				$this->parentState = $this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen];																																																																																																
				if($this->lastState === 'FunctionArgumentIdentifier') {
					$this->state = 'FunctionArgumentComma';
					$expect = 0;
					$expected = 'FunctionArgumentIdentifier';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->parentState === 'ArrayOpen' || $this->lastState === 'ArrayOpen') {
					$this->state = 'ArrayComma';															
				} else if($this->lastState === 'FunctionExpressionArgumentIdentifier') {
					$this->state = 'FunctionExpressionArgumentComma';
					$expect = 0;
					$expected = 'FunctionExpressionArgumentIdentifier';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;		
				} else if($this->parentState === 'ParenExpressionOpen') {
					$this->state = 'ParenExpressionComma';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->isObjectLiteral[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen]) {
					$this->state = 'ObjectLiteralComma';
					$expect = 0;
					$expected = 'ObjectLiteralIdentifier';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				} else if($this->isVar[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen]) {
					$this->state = 'VarComma';
					$expected = 'Identifier';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;	
				} else if($this->isTernary[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen]) {
					$this->error('Syntax error $expected :');				
				} else {
					$this->state = 'Comma';
					$expected = 0;
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
				}
				$this->outputLine .=  ',';
				$this->pos++;
				$this->left = 0;
				$last = self::COMMA;
			} else if($this->chr === self::PERIOD) {
				if($this->left) {							
					$this->state = 'IdentifierDot';								
				} else {
					$this->error('Unexpected . Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$expected = 'Identifier';
				$expected2 = 0;
				$expected3 = 0;
				$expected4 = 0;
				$expect = 0;
				$this->outputLine .=  '.';
				$this->pos++;
				$this->left = 0;
			} else if($this->chr === self::COLON) {
				$this->parentState = $this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen];								
				if($this->isTernary[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen]) {
					$this->state = 'TernaryColon';
					$this->isTernary[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen]--;
					$this->ternaryCount--;
				} else if($rules['ObjectLiteralColon'][$this->lastState]) {
					$this->state = 'ObjectLiteralColon';
					$expected = 0;
					$expected1 = '';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;																								
					$this->isObjectLiteral[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 1;				
				} else if($this->isCase[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] || $this->lastState === 'Default') {
					$this->state = 'SwitchColon';
					if($this->lastState === 'Case') {
						$this->error('Syntax error');
					}
					$expected = 0;
					$expected1 = '';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					if($this->lastState !== 'Default') {
						$this->isCase[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 0;
						$this->caseCount--;	
					}						
				} else if(!$this->parentState) {
					$this->state = 'LabelColon';
				} else {
					$this->error('Unexpected : Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->outputLine .=  ':';
				$this->pos++;
				$this->left = 0;
				$last = self::COLON;												
			} else if($this->chr === self::SEMI_COLON) {				
				$this->parentState = $this->parentStates[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen];									
				if($this->isFor[$this->lookupSquare.''.$this->lookupCurly.''.($this->lookupParen-1)] && !$this->isForIn[$this->lookupSquare.''.$this->lookupCurly.''.($this->lookupParen-1)]) {
					$this->state = 'ForSemi';
					$this->outputLine .=  ';';
					if($this->isFor[$this->lookupSquare.''.$this->lookupCurly.''.($this->lookupParen-1)] > 2) {
						$this->error('Syntax error unexpected for semi ;');
					}
					$this->isFor[$this->lookupSquare.''.$this->lookupCurly.''.($this->lookupParen-1)]++;
					$this->isVar[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 0;																						
				} else {								
					$this->state = 'EndStatement';
					if($this->lastState !== 'EndStatement') {
						$this->outputLine .=  ';';	
					}
					$this->isVar[$this->lookupSquare.''.$this->lookupCurly.''.$this->lookupParen] = 0;					
				}						
				$this->pos++;
				$this->left = 0;
				$last = self::SEMI_COLON;
			} else if($this->chr === self::EXCLAMATION_MARK) {
				$next2 = $this->charCodeAt($code,$this->pos+2);						
				if($this->chr === self::EXCLAMATION_MARK && $next !== self::EQUAL && !$this->left) {
					$this->state = 'Not';
					$this->outputLine .=  ' ! ';
					$this->pos++;
				} else if($this->chr === self::EXCLAMATION_MARK && $next === self::EQUAL && $next2 !== self::EQUAL) {
					$this->state = 'NotEqual';
					$this->outputLine .=  '!=';
					$this->pos+=2;
				} else if($this->chr === self::EXCLAMATION_MARK && $next === self::EQUAL && $next2 === self::EQUAL) {
					$this->state = 'StrictNotEqual';
					$this->outputLine .=  '!==';
					$this->pos+=3;							
				} else {
					$this->error('Unexpected !. Cannot follow '.$this->lastState.'.output:'.$this->output);
				}			
				$this->left = 0;				
			} else if($this->chr === self::TILDE) {
				if($this->chr === self::TILDE && !$this->left) {
					$this->state = 'BitwiseNot';
					$this->outputLine .=  '~';
					$this->pos++;												
				} else {
					$this->error('Unexpected ~ Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;	
			} else if($this->chr === self::PLUS) {
				if($this->chr === self::PLUS && $next === self::PLUS && $this->left) {
					$this->state = '$this->postfixIncrement';
					$this->outputLine .=  '++';
					$this->pos+=2;
				} else if($this->chr === self::PLUS && $next === self::PLUS && !$this->left) {
					$this->state = 'PrefixIncrement';
					$this->outputLine .=  '++';
					$this->pos+=2;						
				} else if($this->chr === self::PLUS && $next === self::EQUAL) {
					$this->state = 'AdditionAssignment';
					$this->outputLine .=  '+=';
					$this->pos+=2;
				} else if($this->chr === self::PLUS && $next !== self::EQUAL && $next !== self::PLUS && $this->left) {
					$this->state = 'Addition';
					$this->outputLine .=  ' + ';
					$this->pos++;
				} else if($this->chr === self::PLUS && $next !== self::EQUAL && $next !== self::PLUS && !$this->left) {
					$this->state = 'UnaryPlus';
					$this->outputLine .=  '+';
					$this->pos++;																	
				} else {
					$this->error('Unexpected + Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;
			} else if($this->chr === self::PIPE) {
				if($this->chr === self::PIPE && $next === self::PIPE) {
					$this->state = 'LogicalOr';
					$this->outputLine .=  '||';
					$this->pos+=2;
				} else if($this->chr === self::PIPE && $next === self::EQUAL) {
					$this->state = 'OrAssignment';
					$this->outputLine .=  '|=';
					$this->pos+=2;
				} else if($this->chr === self::PIPE && $next !== self::PIPE && $next !== self::EQUAL) {
					$this->state = 'BitwiseOr';
					$this->outputLine .=  ' | ';
					$this->pos++;						
				} else {
					$this->error('Unexpected | Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;
			} else if($this->chr === self::CARET) {	
				if($this->chr === self::CARET && $next === self::EQUAL) {
					$this->state = 'XorAssignment';
					$this->outputLine .=  '^=';
					$this->pos+=2;
				} else if($this->chr === self::CARET && $next !== self::EQUAL) {
					$this->state = 'Xor';
					$this->outputLine .=  ' ^ ';
					$this->pos++;						
				} else {
					$this->error('Unexpected ^. Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;
			} else if($this->chr === self::PERCENT) {
				if($this->chr === self::PERCENT && $next === self::EQUAL) {
					$this->state = 'ModulusAssignment';
					$this->outputLine .=  '%=';
					$this->pos+=2;
				} else if($this->chr === self::PERCENT && $next !== self::EQUAL) {
					$this->state = 'Modulus';
					$this->outputLine .=  ' % ';
					$this->pos++;						
				} else {
					$this->error('Unexpected % Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;								
			} else if($this->chr === self::AMPERSAND) {
				if($this->chr === self::AMPERSAND && $next === self::AMPERSAND) {
					$this->state = 'LogicalAnd';
					$this->outputLine .=  '&&';
					$this->pos+=2;
				} else if($this->chr === self::AMPERSAND && $next === self::EQUAL) {
					$this->state = 'AndAssignment';
					$this->outputLine .=  '&=';
					$this->pos+=2;
				} else if($this->chr === self::AMPERSAND && $next !== self::AMPERSAND && $next !== self::EQUAL) {
					$this->state = 'BitwiseAnd';
					$this->outputLine .=  ' & ';
					$this->pos++;						
				} else {
					$this->error('Unexpected & Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;	
			} else if($this->chr === self::EQUAL) {
				$next2 = $this->charCodeAt($code,$this->pos+2);						
				if($this->chr === self::EQUAL && $next !== self::EQUAL) {
					$this->state = 'EqualAssignment';
					$this->outputLine .=  ' = ';
					$this->pos++;
				} else if($this->chr === self::EQUAL && $next === self::EQUAL && $next2 !== self::EQUAL) {
					$this->state = 'Equal';
					$this->outputLine .=  '==';
					$this->pos+=2;
				} else if($this->chr === self::EQUAL && $next === self::EQUAL && $next2 === self::EQUAL) {
					$this->state = 'StrictEqual';
					$this->outputLine .=  '===';
					$this->pos+=3;
				} else {
					$this->error('Unexpected = Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;																											
			} else if($this->chr === self::GREATER_THAN) {
				$next2 = $this->charCodeAt($code,$this->pos+2);
				$next3 = $this->charCodeAt($code,$this->pos+3);
				if($this->chr === self::GREATER_THAN && $next == self::GREATER_THAN && $next2 == self::GREATER_THAN && $next3 === self::EQUAL) {
					$this->state = 'ZeroRightShiftAssignment';
					$this->outputLine .=  '>>>=';
					$this->pos+=4;												
				} else if($this->chr === self::GREATER_THAN && $next == self::GREATER_THAN && $next2 == self::GREATER_THAN) {
					$this->state = 'ZeroRightShift';
					$this->outputLine .=  '>>>';
					$this->pos+=3;	
				} else if($this->chr === self::GREATER_THAN && $next == self::GREATER_THAN && $next2 == self::EQUAL) {
					$this->state = 'RightShiftAssignment';
					$this->outputLine .=  '>>=';
					$this->pos+=3;												
				} else if($this->chr === self::GREATER_THAN && $next == self::GREATER_THAN) {
					$this->state = 'RightShift';
					$this->outputLine .=  '>>';
					$this->pos+=2;
				} else if($this->chr === self::GREATER_THAN && $next !== self::EQUAL) {
					$this->state = 'GreaterThan';
					$this->outputLine .=  ' > ';
					$this->pos++;
				} else if($this->chr === self::GREATER_THAN && $next === self::EQUAL) {
					$this->state = 'GreaterThanEqual';
					$this->outputLine .=  '>=';
					$this->pos+=2;						
				} else {
					$this->error('Unexpected > Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;		
			} else if($this->chr === self::LESS_THAN) {
				$next2 = $this->charCodeAt($code,$this->pos+2);	
				if($this->chr === self::LESS_THAN && $next === self::LESS_THAN && $next2 === self::EQUAL) {
					$this->state = 'LeftShiftAssignment';
					$this->outputLine .=  '<<=';
					$this->pos+=3;
				}else if($this->chr === self::LESS_THAN && $next === self::LESS_THAN) {
					$this->state = 'LeftShift';
					$this->outputLine .=  '<<';
					$this->pos+=2;
				} else if($this->chr === self::LESS_THAN && $next !== self::EQUAL) {
					$this->state = 'LessThan';
					$this->outputLine .=  ' < ';
					$this->pos++;
				} else if($this->chr === self::LESS_THAN && $next === self::EQUAL) {
					$this->state = 'LessThanEqual';
					$this->outputLine .=  '<=';
					$this->pos+=2;						
				} else {
					$this->error('Unexpected < Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;
			} else if($this->chr === self::ASTERIX) {											
				if($this->chr === self::ASTERIX && $next !== self::EQUAL) {
					$this->state = 'Multiply';
					$this->outputLine .=  ' * ';
					$this->pos++;
				} else if($this->chr === self::ASTERIX && $next === self::EQUAL) {
					$this->state = 'MultiplyAssignment';
					$this->outputLine .=  '*=';
					$this->pos+=2;						
				} else {
					$this->error('Unexpected * Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;																						
			} else if($this->chr === self::MINUS) {
				if($this->chr === self::MINUS && $next === self::MINUS && $this->left) {
					$this->state = '$this->postfixDeincrement';
					$this->outputLine .=  '--';
					$this->pos+=2;
				} else if($this->chr === self::MINUS && $next === self::MINUS && !$this->left) {
					$this->state = 'PrefixDeincrement';
					$this->outputLine .=  '--';
					$this->pos+=2;						
				} else if($this->chr === self::MINUS && $next === self::EQUAL) {
					$this->state = 'MinusAssignment';
					$this->outputLine .=  '-=';
					$this->pos+=2;
				} else if($this->chr === self::MINUS && $next !== self::EQUAL && $next !== self::MINUS && $this->left) {
					$this->state = 'Minus';
					$this->outputLine .=  ' - ';
					$this->pos++;
				} else if($this->chr === self::MINUS && $next !== self::EQUAL && $next !== self::MINUS && !$this->left) {
					$this->state = 'UnaryMinus';
					$this->outputLine .=  '-';
					$this->pos++;																	
				} else {					
					$this->error('Unexpected - Cannot follow '.$this->lastState.'.output:'.$this->output);
				}
				$this->left = 0;																			
			} else if($this->chr === self::SINGLE_QUOTE || $this->chr === self::DOUBLE_QUOTE) {															
				if($this->lastState === 'ObjectLiteralCurlyOpen' || $this->lastState === 'ObjectLiteralComma') {
					$this->state = 'ObjectLiteralIdentifierString';
					$this->left = 0;
					$expected = 'ObjectLiteralColon';
					$expected2 = 0;
					$expected3 = 0;
					$expected4 = 0;
					$expect = 0;	
				} else {
					$this->state = 'String';
					$this->left = 1;
				}							
				$this->states = array('escaping'=> 0, 'complete'=> 0);
				$this->states[$this->chr] = 1;						
				$this->outputLine .=  $this->charAt($code, $this->pos);				
				$this->pos++;
				if($this->state === 'ObjectLiteralIdentifierString') {
					if($options['rewrite']) {
						$this->outputLine .=  $this->scoping;
					}	
				}
				for(;;) {					
					$this->chr = $this->charCodeAt($code,$this->pos);
					$next = $this->charCodeAt($code,$this->pos+1);							
					if($this->chr === self::SINGLE_QUOTE && !$this->states['escaping'] && $this->states[self::SINGLE_QUOTE]) {
	                    $this->states['complete'] = 1;                 
	                } else if($this->chr === self::DOUBLE_QUOTE && !$this->states['escaping'] && $this->states[self::DOUBLE_QUOTE]) {
	                    $this->states['complete'] = 1;
	                } else if($this->chr === self::BACKSLASH && !$this->states['escaping'] && ($next === self::NEWLINE || $next === self::CARRIAGE_RETURN || $next === self::LINE_SEPARATOR || $next === self::PARAGRAPH_SEPARATOR) ) {				                    
	                    $this->pos+=2;
	                    continue 1;                                                
	                } else if($this->chr === self::BACKSLASH && !$this->states['escaping']) {
	                    $this->states['escaping'] = 1;				                
	                } else if($this->chr === self::BACKSLASH && $this->states['escaping']) {
	                    $this->states['escaping'] = 0;				                
	                } else if(($this->chr === self::NEWLINE || $this->chr === self::CARRIAGE_RETURN || $this->chr === self::LINE_SEPARATOR || $this->chr === self::PARAGRAPH_SEPARATOR) && !$this->states['escaping']) {
	                    $this->error('Unterminated string literal');
	                } else if($this->states['escaping']) {
	                    $this->states['escaping'] = 0;
	                }                            
	                if($this->pos + 1 > $length) {
	                    $this->error('Unterminated string literal');
	                }
	                if($this->states['complete'] && $this->state === 'ObjectLiteralIdentifierString') {
						if($options['rewrite']) {	
							$this->outputLine .=  $this->scoping;
						}	
					}                                                                       
	                $this->outputLine .=  $this->charAt($code, $this->pos);
					$this->pos++;
	                if($this->states['complete']) {	                                            	                            	
	                    break 1;
	                }							
				}																														
			} else {						
				$this->error('Unable to parse '. $this->charAt($code, $this->pos));
			}															
			
			if($this->state === 'Nothing') {						    
				$this->error('No state defined for char:' . $this->charAt($code, $this->pos));
			}
			
			if(!$rules[$this->state]) {
				$this->error('state does not exist in the rules:' . $this->state);
			}												                       
			
			if(!$rules[$this->state][$this->lastState] && $newLineFlag) {						    						    						    
				$this->asi();												
			}
			
			$this->output .= $this->outputLine;						
			 
			if(!$rules[$this->state][$this->lastState]) {																							
				$this->error('Unexpected ' . $this->state . '. Cannot follow '.$this->lastState.'.output:'.$this->output);
			} else if((($expected && $expected !== $this->state) || ($expected2 && $expected2 !== $this->state) || ($expected3 && $expected3 !== $this->state) || ($expected4 && $expected4 !== $this->state)) && $expect === 1) {
				$this->msg = 'expected ' . $expected;
				if($expected2) {
					$this->msg = $this->msg . ' or ' . $expected2;
				}
				if($expected3) {
					$this->msg = $this->msg . ' or ' . $expected3;
				}
				if($expected4) {
					$this->msg = $this->msg . ' or ' . $expected4;
				}
				$this->msg = $this->msg . '. But got '.$this->state . ' with $last $this->state:'.$this->lastState.', output:'.$this->output;
				$this->error($this->msg);
			}
			
			if($parseTree){							
				$parseTreeOutput .= '<'.$this->state.'>' . $this->outputLine . '</'.$this->state.'>';
			}
			$this->lastState = $this->state;																				
			$newLineFlag = 0;																									
		}	
		if((($expected && $expected !== $this->state) || ($expected2 && $expected2 !== $this->state) || ($expected3 && $expected3 !== $this->state) || ($expected4 && $expected4 !== $this->state))) {
			$this->msg = 'expected ' . $expected;
			if($expected2) {
				$this->msg = $this->msg . ' or ' . $expected2;
			}
			if($expected3) {
				$this->msg = $this->msg . ' or ' . $expected3;
			}
			if($expected4) {
				$this->msg = $this->msg . ' or ' . $expected4;
			}
			$this->msg = $this->msg . '. But got '.$this->state . ' with $last state:'.$this->lastState . ', output:'.$this->output;
			$this->error($this->msg);
		}
						
		if($this->lastState === 'IfStatementParenClose') {
			$this->error('Syntax error');	
		}
		
		if($this->lookupSquare) {
			$this->error('Syntax error unmatched [');
		} else if($this->lookupCurly) {
			$this->error('Syntax error unmatched {');
		} else if($this->lookupParen) {
			$this->error('Syntax error unmatched (');
		} else if($this->caseCount) {
			$this->error('Syntax error unmatched case');
		}
		
		if($options['parseTree']) {						
        	$this->parseTree = $parseTreeOutput;
        }	
		$this->valid = true;	                             													
		return $this->output;
	}
}
?>