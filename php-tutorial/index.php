<?php

mysql_connect("localhost", "username", "password");
mysql_select_db("database");

function Format($Input)
{
	foreach($Input as $Key=>$Value)
	{
		switch(key($Value))
		{
			case "Code":
				$Output .= "<div class=\"code\" onClick=\"javascript:OpenCode('{$Value['Code']['ID']}')\"><pre>{$Value['Code']['Text']}</pre></div>";
			break;

			case "Text":
				if((empty($Output)) || (key($Input[$Key - 1]) == "Code"))
					$Output .= $Value['Text'];
				else
					$Output .= "<br /><br />".$Value['Text'];
			break;

			case "Title":
				if((empty($Output)) || (key($Input[$Key - 1]) == "Code"))
					$Output .= "<span style=\"font-size:14pt; font-weight:bold;\">{$Value['Title']}</span>";
				else
					$Output .= "<br /><br /><span style=\"font-size:14pt; font-weight:bold;\">{$Value['Title']}</span>";
			break;
		}
	}

	return $Output;
}

function Clean($Input, $Type="text")
{
	if($Type == "textarea")
		$Break = "<br />";

	return trim(str_replace(array("<", ">", "\"", "'", "\\", "`", "\r", "\n"), array("&lt;", "&gt;", "&#34;", "&#39;", "&#92", "&#96;", "", "$Break"), stripslashes($Input)));
}

$Go = explode("/", strtolower($_GET['Go']));
$Tab = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

switch($Go[0])
{
	case "code":
		switch($Go[1])
		{
			case "1":
				echo "A string of characters!";
			break;

			case "2":
				echo "One is: 4<br />";
				echo "Two is: 3<br />";
				echo "Together they are: 7";
			break;

			case "3":
				$AnArray = array("The first value!", "Some more data","Arrays can contain anything a variable can!",4, 10, 44, "See? Numbers!", TRUE, FALSE);

				$AnotherArray[] = "This is a...";
				$AnotherArray[] = "different way of doing";
				$AnotherArray[] = "the same thing!";

				echo "<pre>";
				echo print_r($AnArray);
				echo "\n";
				echo print_r($AnotherArray);
				echo "</pre>";
			break;

			case "4":
				echo "Dos<br />text that you want!!";
			break;

			case "5":
				echo "The number is ten!";
			break;

			case "6":
				echo "The number is not nine!";
			break;

			case "7":
				echo "This is a good amount.";
			break;

			case "8":
				echo "We still need more fruit!";
			break;

			case "9":
				echo "We need more oranges!<br />";
			break;

			case "10":
				$Counter = 100;

				while($Counter > 0)
				{
					echo "Hello<br />";
					$Counter--;
				}
			break;

			case "11":
				$Counter = 10;

				while($Counter > 0)
				{
					echo "Hello, your number is: $Counter.<br />";
					$Counter--;
				}

				echo "All finished!";
			break;

			case "12":
				for($Counter = 1; $Counter <= 10; $Counter++)
				{
					echo "Hello, your number is: $Counter.<br />";
				}

				echo "All finished!";
			break;

			case "13":
				$MyName[] = "Brian";
				$MyName[] = "Michael";
				$MyName[] = "Willicus";
				$MyName[] = "Tricky";
				$MyName[] = "Danger";
				$MyName[] = "Hoffmann";
				$MyName[] = "Cuttle";
				$MyName[] = "Schaut";

				foreach($MyName as $Name)
				{
					$FullName = $FullName." $Name";
				}

				echo "Brian's full name is: $FullName.";
			break;

			case "14":
				echo "hey you!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />listen!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br />butts!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
			break;

			case "15":
				echo "hey!!<br />WAKE UP!!!<br />GOOD EVENING FINE SIRS!!!!!!!!!!! FUCK YOU, I AM CALLING MY ATTORNEY<br />";
			break;

			case "16":
				$Foo = "hello this is a string!";
				$Bar = 8371;

				var_dump(empty($Foo));
				echo "<br /><br />";

				var_dump(is_numeric($Foo));
				echo "<br />";
				var_dump(is_numeric($Bar));
				echo "<br /><br />";

				var_dump(strlen($Foo));
				echo "<br />";
				var_dump(strlen($Bar));
			break;

			case "17":
				echo "goodbye how are you doing today?<br />hey have a nice day you man I'm gonna go for a walk on your dog and then cook him on my grill and then I'm going to have a nice day my grill<br /><br />Alan Ross";
			break;

			case "18":
				if(empty($_POST))
				{
					$Address = "http://wetfish.net/php/?Hello=World&This=Is a test";

					$_POST['Hello'] = "World";
					$_POST['This'] = "Is a test";
				}
				else
				{
					$Address = "http://wetfish.net/php/?";
					$Counter = 0;

					foreach($_POST as $Key=>$Value)
					{
						if($Counter != 0)
							$Address .= "&";

						$Address .= "$Key=$Value";

						$Counter++;					
					}
				}

				echo "<form onkeypress=\"return event.keyCode!=13\">Pretend Address Bar: <input type=\"text\" name=\"Address\" id=\"Address\" size=\"50\" value=\"$Address\">&nbsp;<input type=\"button\" value=\"Go\" onclick=\"javascript:GetExample('get')\"></form><hr />";

				echo "<pre>".print_r($_POST, TRUE)."</pre>";
			break;

			case "19":
				echo "<form onkeypress=\"return event.keyCode!=13\">";
				echo "Name: <input type='text' name='Name' id='Name'><br />";
				echo "Password: <input type='password' name='Password' id='Password'><br />";
				echo "<input type='button' value='Submit' onclick=\"javascript:GetExample('post')\">";
				echo "</form>";

				if(!empty($_POST))
				{
					echo "<hr />";
					echo "<pre>";
					echo print_r($_POST, TRUE);
					echo "</pre>";
				} 
			break;

			case "20":
				setcookie("CookieName", "Om nom nom", time() + 1800);
				setcookie("AnotherCookie", "Nom om nom", 1230767999);

				echo $_COOKIE['CookieName']."<br />".$_COOKIE['AnotherCookie'];
			break;

			case "21":
				if(empty($_POST))
				{
					echo "<form onkeypress=\"return event.keyCode!=13\">";
					echo "Name: <input type='text' name='Name' id='Name'><br />";
					echo "Age: <input type='text' name='Age' id='Age'><br />";
					echo "<input type='button' value='Submit' onclick=\"javascript:GetExample('errors')\">";
					echo "</form>";
				}
				else
				{
					$Name = Clean($_POST['Name']);
					$Age = Clean($_POST['Age']);

					if(($Name == "") || ($Age == ""))
						$Error = "Error: You did not fill out all of the fields!";

					if(strlen($Name) > 32)
						$Error = "Error: Your name is too long.";

					if(!is_numeric($Age))
						$Error = "Error: Your age must be a number.";
					elseif($Age > 99)
						$Error = "Error: I think you're lying.";

					if(empty($Error))
					{
						echo "Good job, $Name! You did it right!";
					}
					else
					{
						echo $Error;
					}
				}
			break;

			case "22":
				echo "<b>Brian Cuttle</b><br />Hello, this is my amazing post on the internet!!<hr />";
			break;

			case "23":
				if(!empty($_POST))
				{
					$Name = Clean($_POST['Name']);
					$Post = Clean($_POST['Post'], "textarea");

					if($Name == "")
						$Errors['Name'] = "Error: You must specify a name.";
					elseif(strlen($Name) > 32)
						$Errors['Name'] = "Error: Your name is too long.";

					if($Post == "")
						$Errors['Post'] = "Error: You must write something!";
					elseif(strlen($Post) > 255)
						$Errors['Post'] = "Error: Your post is too long.";

					if(empty($Errors))
					{
						mysql_query("INSERT INTO `Example` VALUES('NULL', '$Name', '$Post')");
						echo "Post successful!";
					}
				}

				if((empty($_POST)) || (!empty($Errors)))
				{
					echo "<form>";
					echo $Errors['Name']."<br />";
					echo "Name: <input type='text' name='Name' id='Name' value='$Name' onkeypress=\"return event.keyCode!=13\"><br />";

					echo $Errors['Post']."<br />";
					echo "Post: <textarea cols='40' rows='8' name='Post' id='Post'>$Post</textarea><br />";

					echo "<input type='button' value='Submit' onclick=\"javascript:GetExample('shoutbox')\">";
					echo "</form>";
					echo "<hr />";

					$Query = mysql_query("SELECT `Name`,`Post` FROM `Example` ORDER BY `ID` DESC LIMIT 30");
					while(list($Name, $Post) = mysql_fetch_array($Query))
					{
						echo "<b>$Name</b><br />";
						echo "$Post<hr />";
					}
				}
			break;
		}
	break;

	case "basics": //variables, arrays, echo, print_r
		$Location = "Basics";

		$Content[]['Text'] = "I can only hope that, in your mind, programming does not conjure up images of, well, bad, confusing things. Programming really is beautiful, or at least, it can be. To start, there are many ways to look at what programming, in its essence, really is. You could say that programming is an extension of language, in that you can not talk to someone in PHP, but you could use PHP to talk to someone. Though, this might not be completely valid, as trees, being genetic programs, don't particularly have any language to \"extend\".";
		$Content[]['Text'] = "A simpler explanation could be that programming is like playing with LEGOs; there are simple pieces you can combine to create ever-increasing complexity. I normally think about my projects this way, especially while just starting to conceptualize what I'm going to need to do. If you're good at planning ahead and thinking about what blocks you want to put where, you'll be better off when you actually get around to sticking them together.";
		$Content[]['Text'] = "However, this \"building block\" analogy is but a single piece of the picture. Another, deeper, view of programming (in <i>my</i> mind at least!) is that you are laying a path for a computer to follow. Without your code, a computer wouldn't know what to do, so you must instruct it on how exactly you'd like it to behave. A way to think about this visually would be to pretend you are a variable. Given specific conditions, you may go one way or another, and you may never get to experience any of the other possibilities. Hopefully this isn't too confusing, as I am certain you have experienced it first hand. When you are programming, you must think of every possibility, but when you are a normal user on a forum, for example, you do not see the admin panel, even though the code for it might be right next to that of your control panel.";
		$Content[]['Text'] = "Ah, but then, what is a variable? And so, you have begun your own path...";
		$Content[]['Title'] = "The Basics";
		$Content[]['Text'] = "In PHP, a variable is any combination of numbers and letters (so long as it doesn't start with numbers!), preceeded by a dollar sign. Variables contain information, any information! Other languages make a greater distinction between the types of information a variable contains, however, PHP is fairly flexable and there are (normally) only two types of distinct information&mdash;strings, and arrays.";
		$Content[]['Code'] = array("ID"=>"0", "Text"=>"&lt;?php\n\n\$Variable = \"A string of characters!\";\n\n?&gt;");
		$Content[]['Text'] = "In this example, the PHP interpreter knows what to do because it sees the text inside of the &lt;?php ?&gt; tag. Without this tag, your code will be nothing more than plain text. Next, a variable with the name \"Variable\" is been defined as \"A string of characters!\". This should be obvious! You should also notice that the statement ended with a semicolon; this is, more or less, the same as a period in English. Nearly every statement in PHP will end with a semicolon.";
		$Content[]['Text'] = "What might not be as obvious about this example is that it doesn't really DO anything. That is, if you make a file with this little piece of PHP in it, you won't see anything. Why is this? Well, you haven't told PHP to show anything!";
		$Content[]['Code'] = array("ID"=>"1", "Text"=>"&lt;?php\n\n\$Variable = \"A string of characters!\";\necho \$Variable;\n\n?&gt;");
		$Content[]['Text'] = "Now you'll see the value of \$Variable! Echo is a language construct of PHP, one of the essential building blocks needed to make a website. You can write all the code you want, but without an echo, the page will be nothing more than blank. (There are a few exceptions to this, of course, there are some other ways to get data onto a page, but echo is the most common.)";
		$Content[]['Text'] = "By the way, if you'd like to see what this, or any of the other code in this tutorial turns into when you upload it to a server, you can click on it and a box should pop up.";
		$Content[]['Code'] = array("ID"=>"2", "Text"=>"&lt;?php\n\n\$Number1 = 4;\n\$Number2 = 3;\n\necho \"One is: \$Number1&lt;br /&gt;\";\necho \"Two is: \$Number2&lt;br /&gt;\";\necho \"Together they are: \";\necho \$Number1 + \$Number2;\n\n?&gt;");
		$Content[]['Text'] = "Now, there you have it&mdash;addition! Subtraction, multiplication, and division all work the same way, too. But let's say you want to have a bunch of variables? Surely there's a better way than writing \$Var1, \$Var2, \$Var3, etc.?";
		$Content[]['Code'] = array("ID"=>"3", "Text"=>"&lt;?php\n\n\$AnArray = array(\"The first value!\", \"Some more data\",\n\"Arrays can contain anything a variable can!\",\n4, 10, 44, \"See? Numbers!\", TRUE, FALSE);\n\n\$AnotherArray[] = \"This is a...\";\n\$AnotherArray[] = \"different way of doing\";\n\$AnotherArray[] = \"the same thing!\";\n\nprint_r(\$AnArray);\nprint_r(\$AnotherArray);\n\n?&gt;");
		$Content[]['Text'] = "Hopefully this isn't too much all at once! Be sure to look at what the examples turn into if you're a bit confused. Hell, copy and paste the code into a text editor and play around with it a bit if you want. Remember, code is just what you are instructing a computer to do; you have the freedom to change it around however you'd like.";
		$Content[]['Text'] = "In any case, the first array should be pretty straightforward; after every comma, you have a new value. One thing that might not be so straightforward is the fact that the values are spread out over 3 lines. Now, there's no reason you'd have to do this in your code, I just did it because it would stretch the page otherwise. However, it is worth noting that the line breaks do not make any difference to the PHP interpreter! You can have any number of linebreaks, tabs, or spaces that you want, because all the interpreter is looking for is the semicolon.";
		$Content[]['Text'] = "The second array is defined using 'empty keys'. Now, I'll go further in depth about what a key is in the next example, but when one is not specified (the brackets are empty), PHP will just add the value to the end of the array. I normally add values to my arrays this way, since it's more visually appealing&mdash;and it shows how an array is literally a bunch of variables stacked ontop of eachother! But wait, what's this print_r thing? Well, print_r is a function, similar to echo, only in that it displays arrays. If you try to echo an array, it'll just say \"Array\", which isn't very useful.";
		$Content[]['Text'] = "And hey, now that we have our values in an array, how do we get them out!? That's where those key things I was talking about come in handy...";
		$Content[]['Code'] = array("ID"=>"4", "Text"=>"&lt;?php\n\n\$Hello[] = \"Uno\";\n\$Hello[] = \"Dos\";\n\$Hello[] = \"Tres\";\n\n\$World['Key'] = \"If you put text in the brackets...\";\n\$World['Two'] = \"the key becomes whatever...\";\n\$World['Text'] = \"text that you want!!\";\n\necho \$Hello[1];\necho \"&lt;br /&gt\";\necho \$World['Text'];\n\n?&gt;");
		$Content[]['Text'] = "In this example, you'll notice that \$Hello[1] is \"Dos\". This is because, by default, arrays start at 0; so \$Hello[0] is \"Uno\". If you don't like this, you can always show the computer who's boss by specifing your own keys! Yeah! Fight the power...or something.";

		$Content = Format($Content);
	break;

	case "blocks": //if, operators (not), elseif, else
		$Location = "Blocks";

		$Content[]['Title'] = "Blocks";
		$Content[]['Text'] = "The next big building block of PHP is, well, block statements. A block statement is normally composed of conditions, brackets, and a big block of text. There are many kinds of blocks, but the simplest would probably be the if.";
		$Content[]['Code'] = array("ID"=>"5", "Text"=>"&lt;?php\n\n\$Number = 10;\n\nif(\$Number == 10)\n{\n{$Tab}echo \"The number is ten!\";\n}\n\n?&gt;");
		$Content[]['Text'] = "In this example, you should see that there is a condition, some brackets, and a block of text. The condition, in this case, is \"\$Number == 10\", this means that only when \$Number is equal to ten (or, to put it another way, only when the condition is true) will the block of code within the brackets execute. You may have also noticed that the if statement did not have its own semicolon! Block statements are the exception to the semicolon rule, since they use brackets.";
		$Content[]['Text'] = "And hey, what's with that double equal sign thing? Well, a single equal sign in PHP is for declaration, so when you want to compare values, you have to use a double equal sign. But what about when you want something to NOT be equal? There are a couple ways to do this, which I'll cover in the next example.";
		$Content[]['Code'] = array("ID"=>"6", "Text"=>"&lt;?php\n\n\$Number = 10;\n\nif(\$Number != 10)\n{\n{$Tab}echo \"The number is not ten!\";\n}\n\nif(!(\$Number == 9))\n{\n{$Tab}echo \"The number is not nine!\";\n}\n\n?&gt;");
		$Content[]['Text'] = "The exclamation point in PHP means \"not\". In the first example, != literally means \"not equal\", so, if \$Number is not equal to ten, the condition is true. However, since \$Number is ten, the condition is not met, and the code is never executed. In the second example, the entire statement is negated by an exclimation point; this can be useful in certain applications, but this is the only time I'll be mentioning it for now.";
		$Content[]['Text'] = "PHP also supports basic mathematical operators, like less than (&lt;), greater than (&gt;), even less than or equal to (&lt;=). But these operators aren't very useful without the introduction of a couple more blocks, elseif and else.";
		$Content[]['Code'] = array("ID"=>"7", "Text"=>"&lt;?php\n\n\$Apples = 10;\n\nif(\$Apples < 2)\n{\n{$Tab}echo \"We're going to need more apples!\";\n}\n\nelseif(\$Apples < 6)\n{\n{$Tab}echo \"We still need more apples!\";\n}\n\nelseif(\$Apples > 20)\n{\n{$Tab}echo \"This is too many apples!\";\n}\n\nelse\n{\n{$Tab}echo \"This is a good amount.\";\n}\n\n?&gt;");
		$Content[]['Text'] = "Elseif works in a very similar way to if, only differing in that it applies to things which do not match the conditions of the if it follows. Else is different in that it has no conditions, and it will apply anything that does not meet the conditions of the previous if and elseifs. If you play around with the value of \$Apples in this example, you will see the output change; this should be obvious.";
		$Content[]['Text'] = "But what if you have two variables you'd like to compare at the same time? Again, there are two ways of doing this...";
		$Content[]['Code'] = array("ID"=>"8", "Text"=>"&lt;?php\n\n\$Apples = 10;\n\$Oranges = 4;\n\nif((\$Apples < 2) || (\$Oranges < 2))\n{\n{$Tab}echo \"We need more fruit than that!\";\n}\n\nelseif((\$Apples < 6) OR (\$Oranges < 6))\n{\n{$Tab}echo \"We still need more fruit!\";\n}\n\nelseif((\$Apples > 20) || (\$Oranges > 20))\n{\n{$Tab}echo \"Woah now, hold the fruit!!\";\n}\n\nelse\n{\n{$Tab}echo \"This is a good amount.\";\n}\n\n?&gt;");
		$Content[]['Text'] = "If you haven't figured it out yet, \"||\" means or. You can also also compare things in the same way by using \"&&\" or \"AND\". I personally prefer using the symbols, since it's easier to hit a key twice than to write a word, but if you prefer to use words, you have the freedom to do so.";
		$Content[]['Text'] = "Also, since a block can contain any code that you'd be able to put anywhere else, you can put an if inside of an if!";
		$Content[]['Code'] = array("ID"=>"9", "Text"=>"&lt;?php\n\n\$Apples = 10;\n\$Oranges = 4;\n\nif((\$Apples + \$Oranges) < 16)\n{\n{$Tab}if(\$Apples < 8)\n{$Tab}{\n{$Tab}{$Tab}echo \"We need more apples!&lt;br /&gt;\";\n{$Tab}}\n{$Tab}if(\$Oranges < 8)\n{$Tab}{\n{$Tab}{$Tab}echo \"We need more oranges!&lt;br /&gt;\";\n{$Tab}}\n}\n\nelse\n{\n{$Tab}echo \"This is a good amount.\";\n}\n\n?&gt;");
		$Content[]['Text'] = "I think that's everything for this section, but I figure I should mention a few things worth remembering. A block will ALWAYS* follow the same, simple pattern of \"condition, opening bracket, block, closing bracket\". Do not let the \"double ending brackets\" of nested blocks confuse you (talking to you, Doates). This is why I always format my code using tabs; if I have everything lined up, it's easy to see what bracket goes where and what my code is doing. Using tabs is not necessary, but be prepared to become very confused if you don't spend the effort properly aligning your code.";
		$Content[]['Text'] = "&nbsp;";
		$Content[]['Text'] = "&nbsp;";
		$Content[]['Text'] = "&nbsp;";
		$Content[]['Text'] = "*Ok, so ALWAYS is a bit of a stretch. If you have an if/elseif/else statement which only has one line in its block, the brackets are optional. However, I have included them here as not to confuse the fuck out of everyone. If it's easier for you to remember that all blocks follow the same pattern, there's no harm done in including the brackets.";
		$Content[]['Text'] = "Anyway...";

		$Content = Format($Content);
	break;

	case "loops": //while, for, foreach, inc/decriment, concatenation
		$Location = "Loops";

		$Content[]['Title'] = "Loops";
		$Content[]['Text'] = "Loops, ah, loops. Now we're getting to the fun stuff&mdash;and I mean that literally, too! Every video game you've ever played is a loop, if your FTP client can upload multiple files, then yep, there's another loop! I might even go so far as to say that you and I, too, are loops, but this isn't the place to get into that...";
		$Content[]['Code'] = array("ID"=>"10", "Text"=>"&lt;?php\n\nwhile(TRUE)\n{\n{$Tab}echo \"Hello!!&lt;br /&gt;\";\n}\n\n?&gt;");
		$Content[]['Text'] = "And there you have it! Your first loop. Now, if you click on this example, the result is not going to be quite true to what you would get if you actually put this code onto your own page. Why? Well, this loop will continue on forever, since the condition is TRUE, and it will never not be TRUE! So until you stop loading the page or the server crashes, you'll get a flood of \"Hello!!\". The example only goes 100 times, but it still gives you a good idea of what it'd look like. The same thing! Repeating! Into infinity!";
		$Content[]['Text'] = "Hopefully you noticed that this while loop is also a block statement. It too, has conditions, brackets, and a block of code to execute. Now that we've got that taken care of, why not do something more practical, like a counter?";
		$Content[]['Code'] = array("ID"=>"11", "Text"=>"&lt;?php\n\n\$Counter = 10;\n\nwhile(\$Counter > 0)\n{\n{$Tab}echo \"Hello, your number is: \$Counter.&lt;br /&gt;\";\n{$Tab}\$Counter--;\n}\n\necho \"All finished!\";\n\n?&gt;");
		$Content[]['Text'] = "In this example, I have introduced the concept of decrementing a variable. When you write a variable with -- after it, you are basically telling php to subtract one from the value; the opposite, incrementing a variable, can be done with ++. Mind you, this can only be done with a variable which contains a number and nothing else. If you try to increment a string, nothing will happen.";
		$Content[]['Code'] = array("ID"=>"12", "Text"=>"&lt;?php\n\nfor(\$Counter = 1; \$Counter <= 10; \$Counter++)\n{\n{$Tab}echo \"Hello, your number is: \$Counter.&lt;br /&gt;\";\n}\n\necho \"All finished!\";\n\n?&gt;");
		$Content[]['Text'] = "This is another type of loop, a for loop. It behaves in a similar way to the while loop, only differing in that you must declare three things. The first thing you declare is the value of the variable the loop relates to. The second is the condition by which the loop to executes. The third is what to do to the variable every time the loop completes (iterates). You might be wondering what the point is of a distinction between these two loops, seeing how similar they are. To put it simply, a for loop is generally used when you know how many times you want the loop to execute, where as a while loop is better suited to continue until a specific condition is met. In these examples, there isn't much difference, but out there in the wonderful world of programming, there is a very big difference!";
		$Content[]['Code'] = array("ID"=>"13", "Text"=>"&lt;?php\n\n\$MyName[] = \"Brian\";\n\$MyName[] = \"Michael\";\n\$MyName[] = \"Willicus\";\n\$MyName[] = \"Tricky\";\n\$MyName[] = \"Danger\";\n\$MyName[] = \"Hoffmann\";\n\$MyName[] = \"Cuttle\";\n\$MyName[] = \"Schaut\";\n\nforeach(\$MyName as \$Name)\n{\n{$Tab}\$FullName = \$FullName.\" \$Name\";\n}\n\necho \"Brian's full name is: \$FullName.\";\n\n?&gt;");
		$Content[]['Text'] = "Once again, I have introduced two new concepts at once! The first is the foreach loop, which takes the values of an array and assigns them to a single variable, once for each value! The second is concatenation. That is, if you notice, there is a period between \$Fullname and \" \$Name\". This period is like glue in PHP, you use it to bring things together! Now, in this example, I could've just put both variables in quotes, but I wanted to explain concatenation now, as it is very useful when dealing with the next section...";

		$Content = Format($Content);
	break;

	case "functions": //writing your own, predefined functions (empty, strlen, is_numeric, str_replace)
		$Location = "Functions";

		$Content[]['Title'] = "Functions";
		$Content[]['Text'] = "Can you feel it yet? Has the tao of programming begun to flow through you? In time, yes, in time, maybe you too will look at a tree and think \"branching algorithm!\", but we're not quite there yet. Right now we're at functions. Well, what's a function then? A function is basically a block of code you can execute on demand! Rather than copying and pasting the same code over and over, you can just write a function and pass variables to it instead. It's really quite nice.";
		$Content[]['Code'] = array("ID"=>"0", "Text"=>"&lt;?php\n\nfunction Yell(\$Input)\n{\n{$Tab}\$Output = \$Input.\"!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\";\n{$Tab}return \$Output;\n}\n\n?&gt;");
		$Content[]['Text'] = "Once again, we're dealing with a block structure, except this time the conditions are input variables. Normally when you make a function, you take some input, do stuff with it, and then return an output. In this case, the output is just the input, with a bunch of exclimation points added. Similar in the way that declaring the value of a variable will do nothing, declaring this function won't do anything until the function is called.";
		$Content[]['Code'] = array("ID"=>"14", "Text"=>"&lt;?php\n\nfunction Yell(\$Input)\n{\n{$Tab}\$Output = \$Input.\"!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\";\n{$Tab}return \$Output;\n}\n\necho Yell(\"hey you\").\"&lt;br /&gt;\".Yell(\"listen\").\"&lt;br /&gt;\";\necho Yell(\"butts\");\n\n?&gt;");
		$Content[]['Text'] = "And there you have it, your first function! But before we continue, I must let you in on a little secret&mdash;PHP comes pre-packaged with many functions, <a href=\"http://www.php.net/manual/en/indexes.php\" target=\"_blank\">many, many functions</a>. For this next example, I'm going to be using the function \"strtoupper()\", which turns all the letters in a string into uppercase!";
		$Content[]['Code'] = array("ID"=>"15", "Text"=>"&lt;?php\n\nfunction Yell(\$Input, \$Loudness=\"loud\")\n{\n{$Tab}if(\$Loudness == \"loud\")\n{$Tab}{$Tab}\$Output = \$Input.\"!!\";\n\n{$Tab}elseif(\$Loudness == \"louder\")\n{$Tab}{$Tab}\$Output = strtoupper(\$Input).\"!!!\";\n\n{$Tab}elseif(\$Loudness == \"rossthefox\")\n{$Tab}{$Tab}\$Output = strtoupper(\$Input).\"!!!!!!!!!!! FUCK YOU, I AM CALLING MY ATTORNEY\";\n\n{$Tab}return \$Output.\"&lt;br /&gt;\";\n}\n\necho Yell(\"hey\");\necho Yell(\"wake up\", \"louder\");\necho Yell(\"good evening fine sirs\", \"rossthefox\");\n\n?&gt;");
		$Content[]['Text'] = "Aside from strtoupper(), you should've noticed another weird thing about this example. The \$Loudness=\"loud\" in the variable declaration means that the default value of \$Loudness is \"loud\". Without this, PHP would give an error, since a function normally needs every variable to behave properly. However, because this variable has a default value, you can call the function and exclude it, as I did with Yell(\"hey\"). Another thing you may have noticed is that I excluded the brackets from the if statements; this was simply done to save space.";
		$Content[]['Text'] = "Some other commonly used pre-defined functions are empty(), strlen(), and is_numeric(). Empty() tells you if a variable or array has any data in it, that is, if it's empty or not; Is_numeric() tells you if a variable is a number or not; and strlen() gives you the length of a string, or, to put it another way, how many characters are in a variable. Also, I'm going to be using a fourth function in this example, var_dump(), instead of echo. Var_dump() is useful, since it shows all of the information about a variable, if you do \"echo FALSE;\", it'll just be blank.";
		$Content[]['Code'] = array("ID"=>"16", "Text"=>"&lt;?php\n\n\$Foo = \"hello this is a string!\";\n\$Bar = 8371;\n\nvar_dump(empty(\$Foo));\necho \"&lt;br /&gt;&lt;br /&gt;\";\n\nvar_dump(is_numeric(\$Foo));\necho \"&lt;br /&gt;\";\nvar_dump(is_numeric(\$Bar));\necho \"&lt;br /&gt;&lt;br /&gt;\";\n\nvar_dump(strlen(\$Foo));\necho \"&lt;br /&gt;\";\nvar_dump(strlen(\$Bar));\n\n?&gt;");
		$Content[]['Text'] = "As you can see, because the variable \$Foo is not empty, empty(\$Foo) returned false. Conversely, since \$Bar is numeric, is_numeric(\$Bar) returned true! It's as simple as that. Really, there's nothing more to it.";
		$Content[]['Text'] = "Anyway, there are two last pre-defined functions I'd like to cover here: str_replace() and list(). Str_replace() takes three inputs, the characters you want to replace, the replacements, and the string you're working with. List() is a bit different, it can take any number of inputs, and it sets them equal to the values of an array. If that doesn't make sense, hopefully it'll make more sense after the example. :)";
		$Content[]['Code'] = array("ID"=>"17", "Text"=>"&lt;?php\n\n\$Greeting = \"hello how are you doing today?\";\n\$BadThings = \"hey fuck you man I'm gonna shit on your dog and\nthen cook him on my grill and then I'm going to fuck my grill\";\n\necho str_replace(\"hello\", \"goodbye\", \$Greeting);\necho \"&lt;br /&gt;\";\necho str_replace(array(\"fuck\", \"shit\"), array(\"have a nice day\", \"go for a walk\"), \$BadThings);\n\n\$Names = array(\"Alan\", \"Ninja\", \"Rouge\", \"Ross\");\nlist(\$FirstName, \$FakeFriend, \$FakeGirlfriend, \$LastName) = \$Names;\necho \"&lt;br /&gt;&lt;br /&gt;\";\necho \"\$FirstName \$LastName\";\n\n?&gt;");
		$Content[]['Text'] = "Why yes, that's right, you can replace the values of an array with the values of another array using str_replace! It's really pretty handy, and it's how just about every forum impliments wordfilters, although there are a few other ways to do it. Hopefully list() is pretty straightforward now, since, as you can see, the values of the array are simply being assigned as the variables listed. And I think that just about covers it...";

		$Content = Format($Content);
	break;

	case "input": //$_GET, $_POST, $_COOKIE, switch
		$Location = "Input";

		$Content[]['Title'] = "Input";
		$Content[]['Text'] = "Input! Yes, input. Hold on to your hats, now, things are about to start getting <i>dynamic!</i>";
		$Content[]['Text'] = "PHP has three main ways of getting input from a user. All three ways are contained by arrays, \$_GET, \$_POST, and \$_COOKIE. \$_GET contains information from the URL of the page, for example, the URL of this page is index.php?Go=input. By using \$_GET['Go'], I can get the value of the page you want to look at!";
		$Content[]['Code'] = array("ID"=>"18", "Text"=>"&lt;?php\n\necho \"&lt;pre&gt;\";\nprint_r(\$_GET);\necho \"&lt;/pre&gt;\";\n\n?&gt;");
		$Content[]['Text'] = "Finally! An example you get to play around with! Feel free to mess around with the values in the example address bar. Once you submit, every change you made is seen by PHP; this is the sort of control you want when making an actual website! You should also take note of the &lt;pre&gt; tag I included in this example, this was done to make the output of print_r() easier to read. In previous examples I have excluded the &lt;pre&gt; tags for the sake of simplicity, however they were still used in the back-end to make the output readable.";
		$Content[]['Text'] = "\$_POST works in a similar way, only differing in that it gets its data from forms on a page. Ever made a post on a forum? A blog? Used Google? Yep, all forms!";
		$Content[]['Code'] = array("ID"=>"19", "Text"=>"&lt;?php\n\necho \"&lt;form&gt;\";\necho \"Name: &lt;input type='text' name='Name'&gt;&lt;br /&gt;\";\necho \"Password: &lt;input type='password' name='Password'&gt;&lt;br /&gt;\";\necho \"&lt;input type='submit' value='Submit'&gt;\";\necho \"&lt;/form&gt;\";\n\nif(!empty(\$_POST))\n{\n{$Tab}echo \"&lt;hr /&gt;\";\n{$Tab}echo \"&lt;pre&gt;\";\n{$Tab}print_r(\$_POST);\n{$Tab}echo \"&lt;/pre&gt;\";\n}\n\n?&gt;");
		$Content[]['Text'] = "Wohoho, now ain't that fancy? Notice, the name of the field on the form is the name of the key in \$_POST.";
		$Content[]['Text'] = "The last type of input I'll be covering here, \$_COOKIE, is different in that it is generally used to store information about a user (such as login information) over a period of time. There are two ways to define a cookie, through the php function setcookie(), and by using javascript. I tend to use javascript since setcookie() requires that you have not sent any information to the browser before it is called; that is, you can't echo anything before you use setcookie().";
		$Content[]['Code'] = array("ID"=>"20", "Text"=>"&lt;?php\n\nsetcookie(\"CookieName\", \"Om nom nom\", time() + 1800);\necho \"&lt;script&gt;document.cookie = 'AnotherCookie = Nom om nom; expires = Mon, 31 Dec 2012 23:59:59 UTC;'&lt;/script&gt\";\n\necho \$_COOKIE['CookieName'].\"&lt;br /&gt;\".\$_COOKIE['AnotherCookie'];\n\n?&gt;");
		$Content[]['Text'] = "If you clicked on that example once and were confused as to why it was blank, click on it again! I'm pretty sure the reason why the page is blank the first time is because PHP has to send the cookies to the browser, but the browser doesn't tell PHP it has any cookies, because it simply didn't have any at the time it made the request. I could be wrong, so come <a href=\"irc://irc.wetfish.net/wetfish\">yell at me on IRC</a> if this proves not to be the case.";
		$Content[]['Text'] = "Anyway, you should notice that in both examples, the cookie has an expiration date. The PHP function setcookie() takes a Unix timestamp (the number of seconds since January 1st, 1970) as its expiration date, where as the Javascript method takes the date in the form of a string. The function time() I used in setcookie() simply returns the current unix timestamp, so I added 1800 seconds, or 30 minutes to it. This means the cookie CookieName will expire in 30 minutes, where as AnotherCookie is set to expire at the next completely arbitrary date people have decided to be afraid of...";

		$Content = Format($Content);
	break;

	case "errors": //Clean(), basics of mysql injection
		$Location = "Error Handling";

		$Content[]['Title'] = "Error Handling";
		$Content[]['Text'] = "Now that users can submit things to your pages, you need to take into account that sometimes they make mistakes, or sometimes they mess with the values on purpose to possibly exploit your code. For these reasons, if you continue a career in programming, you will hear this time and time again: <b>NEVER</b> trust data from a client, ever! Always clean your input! Check every possibility! Never, ever trust browser-side code like \"maxlength\" or \"disabled\" in a form! A malicious user can always use their own source to submit data to your page, so always do your checking server-side.";
		$Content[]['Text'] = "Cracking into computers, in its essense, is simply finding mistakes. Finding a way to include some data which makes a computer interpret its code in a way the original programmer did not intend, for example. For this reason, error handling is more tedious than anything else, so I'll keep this section brief.";
		$Content[]['Code'] = array("ID"=>"0", "Text"=>"SELECT * FROM MyTable WHERE User = \$Username");
		$Content[]['Text'] = "If you're freaking out because you have no idea wtf is going on, don't worry. This is just an SQL query, the syntax will be covered in depth in the next section. For now I just want to give you an example of what I'm talking about. Let's say you got the variable \$Username from a form and didn't do anything with it. Now, if someone wanted to, they could make their name \"admin OR 1 = 1\" This could potentially give someone access to an account they're not suposed to be able to access, since the query will evaluate to be true, on account of the fact that 1 will always equal 1. However, because I normally use queries to get a password and confirm the login that way, this sort of attack probably wouldn't work. Ultimately, it all depends on the query you're dealing with and how you get data from it. There are several ways to potentially get a list of usernames and passwords from a database, or insert data into places it's not supposed to be. This is why you should always properly format your queries with quotes.";
		$Content[]['Code'] = array("ID"=>"0", "Text"=>"SELECT `*` FROM `MyTable` WHERE `User` = '\$Username'");
		$Content[]['Text'] = "Notice how the user input is in single quotes, where as all the other stuff that we haven't discussed yet is in accent marks. This is a good step, but still not quite there! The last thing you need to do is clean the input to make sure the user can't put any quotes or other funny characters into the query that could potentially mess it up. Which is why I use this...";
		$Content[]['Code'] = array("ID"=>"0", "Text"=>"&lt;?php\n\nfunction Clean(\$Input, \$Type=\"text\")\n{\n{$Tab}if(\$Type == \"textarea\")\n{$Tab}{$Tab}\$Break = \"&lt;br /&gt;\";\n\n{$Tab}return trim(str_replace(array(\"&lt;\", \"&gt;\", \"&#92;\"\", \"'\", \"&#92;&#92;\", \"`\", \"&#92;r\", \"&#92;n\"),\narray(\"&amp;lt;\", \"&amp;gt;\", \"&amp;#34;\", \"&amp;#39;\", \"&amp;#92\", \"&amp;#96;\", \"\", \"\$Break\"), stripslashes(\$Input)));\n}\n\n?&gt;");
		$Content[]['Text'] = "I use this function on all my input, just to be safe. The following example does not define this function (for the sake of space), but assumes it is included...";
		$Content[]['Code'] = array("ID"=>"21", "Text"=>"&lt;?php\n\nif(empty(\$_POST))\n{\n{$Tab}echo \"&lt;form&gt;\";\n{$Tab}echo \"Name: &lt;input type='text' name='Name'&gt;&lt;br /&gt;\";\n{$Tab}echo \"Age: &lt;input type='text' name='Age'&gt;&lt;br /&gt;\";\n{$Tab}echo \"&lt;input type='submit' value='Submit'&gt;\";\n{$Tab}echo \"&lt;/form&gt;\";\n}\nelse\n{\n{$Tab}\$Name = Clean(\$_POST['Name']);\n{$Tab}\$Age = Clean(\$_POST['Age']);\n\n{$Tab}if((\$Name == \"\") || (\$Age == \"\"))\n{$Tab}{$Tab}\$Error = \"Error: You did not fill out all of the fields!\";\n\n{$Tab}if(strlen(\$Name) &gt; 32)\n{$Tab}{$Tab}\$Error = \"Error: Your name is too long.\";\n\n{$Tab}if(!is_numeric(\$Age))\n{$Tab}{$Tab}\$Error = \"Error: Your age must be a number.\";\n{$Tab}elseif(\$Age &gt; 99)\n{$Tab}{$Tab}\$Error = \"Error: I think you're lying.\";\n\n{$Tab}if(empty(\$Error))\n{$Tab}{\n{$Tab}{$Tab}echo \"Good job, \$Name! You did it right!\";\n{$Tab}}\n{$Tab}else\n{$Tab}{\n{$Tab}{$Tab}echo \$Error;\n{$Tab}}\n}\n\n?&gt;");
		$Content[]['Text'] = "And there you have it, the essence of error handling. Honestly, the majority of the code I do is verifying input. It's tedious, it's annoying, but it has to be done; trust me, the annoyance of being hacked is significantly greater...";

		$Content = Format($Content);
	break;

	case "mysql": //mysql_query(), mysql_fetch_array(), phpmyadmin
		$Location = "MySQL";

		$Content[]['Title'] = "MySQL";
		$Content[]['Text'] = "MySQL is a database management language; you use queries to store and retrieve information from a database. The syntax is fairly straightforward, with the two most common types of queries being \"INSERT\", and \"SELECT\". A database is quite literally a collection of information-tables; each divided up into columns and rows. You add rows to a table by using INSERT, and you grab the data by using SELECT. But first, you need to make the table itself!";
		$Content[]['Code'] = array("ID"=>"0", "Text"=>"CREATE TABLE `Example` (\n`ID` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,\n`Name` VARCHAR( 32 ) NOT NULL ,\n`Post` VARCHAR( 255 ) NOT NULL\n);");
		$Content[]['Text'] = "This query creates a table named \"Example\", with three columns. The first column, \"ID\" is necessary for every table you make; if you notice, the options given to it, are primary key and auto_increment. Every table needs a primary key, so MySQL knows how to organize the data, and auto_increment means that every time you add new information to the table, the value of ID will be increased by one. Also notice that you have to specify the type of information every column contains. ID is an integer with a maximum length of 10 characters, where as Name and Post can be any combination of letters, numbers, or symbols, so long as the length of the string is less than 32 and 255 characters respectively.";
		$Content[]['Text'] = "There are many other types in MySQL, but this isn't the place for me to explain them in depth. For a good guide on what all the different types mean, you can <a href=\"http://www.htmlite.com/mysql003.php\" target=\"_blank\">use this site</a>. With that said, if you have an account on mitch, you shouldn't need to worry about creating tables through queries, since you can just use <a href=\"http://wetfish.net/php_your_admin/\" target=\"_blank\">PHP My Admin!</a>";
		$Content[]['Text'] = "Now we know how to make a table, but we haven't connected to the database yet!";
		$Content[]['Code'] = array("ID"=>"0", "Text"=>"&lt;?php\n\nmysql_connect(\"localhost\", \"username\", \"password\");\nmysql_select_db(\"database\");\n\n\$Query = mysql_query(\"SHOW TABLES FROM `database`\");\nwhile(\$Data = mysql_fetch_array(\$Query))\n{\n{$Tab}echo \"\$Data[0]&lt;br /&gt;\";\n}\n\n?&gt;");
		$Content[]['Text'] = "Here you see all of the basic components you'll need to use MySQL in PHP. Of course, you'll need to modify the \"username\", \"password\", and \"database\" values, but other than that, mysql_connect() and mysql_select_db() are the only two things you need to do to start talking to a database. Once you're connected, you use mysql_query() to send and recieve information. In this example, I simply list all the tables in a database, but that doesn't do you much good if you want to put information into a table!";
		$Content[]['Code'] = array("ID"=>"22", "Text"=>"&lt;?php\n\nmysql_query(\"INSERT INTO `Example` VALUES ('NULL', 'Brian Cuttle',\n'Hello, this is my amazing post on the internet!!')\");\n\n\$Query = mysql_query(\"SELECT `Name`,`Post` FROM `Example` ORDER BY `ID` DESC\");\nwhile(list(\$Name, \$Post) = mysql_fetch_array(\$Query))\n{\n{$Tab}echo \"&lt;b&gt;\$Name&lt;/b&gt;&lt;br /&gt;\$Post&lt;hr /&gt;\";\n}\n\n?&gt;");
		$Content[]['Text'] = "I omitted mysql_connect() and mysql_select_db() for the sake of space, but remember, without calling them first, you can never make any queries!";
		$Content[]['Text'] = "In any case, I'm hoping that this example is pretty straightforward. The first query inserts some values into the table, and the second query uses a loop to get them all out. The reason the value of ID is 'NULL' in the INSERT query is because, as  I said before, ID auto_increments. If you specify no value, MySQL will take care of it for you! In the second query, I use the function mysql_fetch_array() to turn the information recieved from the database into an array, which I then split into individual variables by using list().";
		$Content[]['Text'] = "Now that we've got that taken care of, we can use what we've learned in the previous sections about user input and error handling to create a little shout box...";
		$Content[]['Code'] = array("ID"=>"23", "Text"=>"&lt;?php\n\nmysql_connect(\"localhost\", \"username\", \"password\");\nmysql_select_db(\"database\");\n\nif(!empty(\$_POST))\n{\n{$Tab}\$Name = Clean(\$_POST['Name']);\n{$Tab}\$Post = Clean(\$_POST['Post'], \"textarea\");\n\n{$Tab}if(\$Name == \"\")\n{$Tab}{$Tab}\$Errors['Name'] = \"Error: You must specify a name.\";\n{$Tab}elseif(strlen(\$Name) &gt; 32)\n{$Tab}{$Tab}\$Errors['Name'] = \"Error: Your name is too long.\";\n\n{$Tab}if(\$Post == \"\")\n{$Tab}{$Tab}\$Errors['Post'] = \"Error: You must write something!\";\n{$Tab}elseif(strlen(\$Post) &gt; 255)\n{$Tab}{$Tab}\$Errors['Post'] = \"Error: Your post is too long.\";\n\n{$Tab}if(empty(\$Errors))\n{$Tab}{\n{$Tab}{$Tab}mysql_query(\"INSERT INTO `Example` VALUES('NULL', '\$Name', '\$Post')\");\n{$Tab}{$Tab}echo \"Post successful!\";\n{$Tab}}\n}\n\nif((empty(\$_POST)) || (!empty(\$Errors)))\n{\n{$Tab}echo \"&lt;form&gt;\";\n{$Tab}echo \$Errors['Name'].\"&lt;br /&gt;\";\n{$Tab}echo \"Name: &lt;input type='text' name='Name' value='\$Name'&gt;&lt;br /&gt;\";\n\n{$Tab}echo \$Errors['Post'].\"&lt;br /&gt;\";\n{$Tab}echo \"Post: &lt;textarea cols='40' rows='8' name='Post'&gt;\$Post&lt;/textarea&gt;&lt;br /&gt;\";\n\n{$Tab}echo \"&lt;input type='submit' value='Submit'&gt;\";\n{$Tab}echo \"&lt;/form&gt;\";\n{$Tab}echo \"&lt;hr /&gt;\";\n\n{$Tab}\$Query = mysql_query(\"SELECT `Name`,`Post` FROM `Example` ORDER BY `ID` DESC LIMIT 30\");\n{$Tab}while(list(\$Name, \$Post) = mysql_fetch_array(\$Query))\n{$Tab}{\n{$Tab}{$Tab}echo \"&lt;b&gt;\$Name&lt;/b&gt;&lt;br /&gt;\";\n{$Tab}{$Tab}echo \"\$Post&lt;hr /&gt;\";\n{$Tab}}\n}\n\n?&gt;");
		$Content[]['Text'] = "And there you have it! If you need to, take a few minutes to digest all that code. There really are only two things which should be unfamiliar to you in this example. The first is \"LIMIT 30\" in the query; this simply means the query is limited to 30 rows. The other thing which might be a little odd is that the errors are above the form in this example. This is done so even if someone makes a mistake, they can see where exctly they made an error, they don't need to press back to fix their error, and so their input does not get lost. Pretty clever, huh?";
		$Content[]['Text'] = "By now, you should be prepared enough to start working on your own websites, but in the event you still want a few more examples to work with, you can check out the guide on how to make your own blog...";

		$Content = Format($Content);
	break;

	case "blog":
		switch($Go[1])
		{
			case "input":
				$Location = "Blog (Input)";

				$Content[]['Title'] = "Make Your Own... Blog (User Input)";
				$Content[]['Text'] = "Now that we know what we need to do, we should start working on that post page! But wait, how do we know what page we're on? Well, let's get a variable from the URL, call it Go, why not, and then we want to use explode() on it. Why? Well, explode() takes a string and splits it into an array every time it sees a character you specify. In this case, we should explode at \"/\", that way, we can format our URL like \"?Go=post/32\".";
				$Content[]['Code'] = array("ID"=>"0", "Text"=>"&lt;?php\n\n\$Go = explode(\"/\", \$_GET['Go']);\n\nswitch(\$Go[0])\n{\n{$Tab}case \"post\":\n{$Tab}{$Tab}//Code for input\n{$Tab}break;\n\n{$Tab}case \"view\":\n{$Tab}{$Tab}//Code for viewing posts\n{$Tab}break;\n\n{$Tab}default:\n{$Tab}{$Tab}//Main page\n{$Tab}break;\n}\n\n?&gt;");
				$Content[]['Text'] = "The text following the double slashes in this example are comments. They don't do anything, except sit there and remind the programmer of what's going on. I probably should have covered them sooner, but oh well, here they are now! Another thing I should have probably covered before now is the switch block. Switch behaves similar to an if/else statement, only differing in that you're only comparing the value of one variable. Rather than writing if(\$Go[0] == \"view\"), elseif(\$Go[0] == \"page\"), etc., I like to use a switch, since it keeps things tidy, especially when dealing with a lot of possibilities.";
				$Content[]['Text'] = "Now that we know what page we're on, we're going to need to figure out if the user is requesting the comment post page, or the main post page. Well, how would we do that? Simple! is_numeric()!";
				$Content[]['Code'] = array("ID"=>"0", "Text"=>"if(is_numeric(\$Go[1]))\n{\n{$Tab}//Code for posting a comment\n}\n\nelse\n{\n{$Tab}//Code for making a blog post\n}");
				$Content[]['Text'] = "If you're confused about \$Go, just think, if the page you're on is \"post/392/hello/butts\", the value of \$Go[0] is going to be \"post\", the value of \$Go[1] is going to be \"392\", \$Go[2] will be \"hello\", and \$Go[3] will be \"butts\". See? It's just an array where every new value starts from where a / used to be!";
				$Content[]['Text'] = "Now that we've got the \"complicated\" stuff taken care of, all that's left to do is make a form to get some input, make sure there's no errors, and put it in a database!";
				$Content[]['Code'] = array("ID"=>"0", "Text"=>"if(!empty(\$_POST))\n{\n{$Tab}\$Author = Clean(\$_POST['Author']);\n{$Tab}\$Password = Clean(\$_POST['Password']);\n{$Tab}\$Post = Clean(\$_POST['Post'], \"textarea\");\n\n{$Tab}if(\$Author == \"\")\n{$Tab}{$Tab}\$Errors['Author'] = \"Error: You must enter a name!\";\n{$Tab}elseif(strlen(\$Author) &gt; 32)\n{$Tab}{$Tab}\$Errors['Author'] = \"Error: Your name is too long!\";\n\n{$Tab}if(\$Password != \"qwerty\")\n{$Tab}{$Tab}\$Errors['Password'] = \"Error: Invalid password.\";\n\n{$Tab}if(\$Post == \"\")\n{$Tab}{$Tab}\$Errors['Post'] = \"Error: You must write something.\";\n{$Tab}elseif(strlen(\$Post) &gt; 5000)\n{$Tab}{$Tab}\$Errors['Post'] = \"Error: Oh come on, your life isn't THAT interesting.\";\n\n{$Tab}if(empty(\$Errors))\n{$Tab}{\n{$Tab}{$Tab}\$Time = time();\n\n{$Tab}{$Tab}mysql_query(\"INSERT INTO `Posts` VALUES('NULL', '\$Time', '0', '\$Author', '\$Post')\");\n{$Tab}{$Tab}\$Content = \"&lt;meta http-equiv='refresh' content='2;url=blog.php'&gt;Post successful!\";\n\n{$Tab}}\n}\n\nif((empty(\$_POST)) || (!empty(\$Errors)))\n{\n{$Tab}\$Content = \"&lt;form method='post'&gt;&lt;table&gt;\";\n\n{$Tab}if(\$Errors['Author'] != \"\")\n{$Tab}{$Tab}\$Content .= \"&lt;tr&gt;&lt;td colspan='2'&gt;\".\$Errors['Author'].\"&lt;/td&gt;&lt;/tr&gt;\";\n{$Tab}\$Content .= \"&lt;tr&gt;&lt;td&gt;Author:&lt;/td&gt;&lt;td&gt;&lt;input type='text' name='Author' value='\$Author'&gt;&lt;/td&gt;&lt;/tr&gt;\";\n\n{$Tab}if(\$Errors['Password'] != \"\")\n{$Tab}{$Tab}\$Content .= \"&lt;tr&gt;&lt;td colspan='2'&gt;\".\$Errors['Password'].\"&lt;/td&gt;&lt;/tr&gt;\";\n{$Tab}\$Content .= \"&lt;tr&gt;&lt;td&gt;Password:&lt;/td&gt;&lt;td&gt;&lt;input type='password' name='Password' value='\$Post'&gt;&lt;/td&gt;&lt;/tr&gt;\";\n\n{$Tab}if(\$Errors['Post'] != \"\")\n{$Tab}{$Tab}\$Content .= \"&lt;tr&gt;&lt;td colspan='2'&gt;\".\$Errors['Post'].\"&lt;/td&gt;&lt;/tr&gt;\";\n{$Tab}\$Content .= \"&lt;tr&gt;&lt;td&gt;Post:&lt;/td&gt;&lt;td&gt;&lt;textarea name='Post' rows='4' cols='40'&gt;\$Post&lt;/textarea&gt;&lt;/td&gt;&lt;/tr&gt;\";\n\n{$Tab}\$Content .= \"&lt;tr&gt;&lt;td colspan='2'&gt;&lt;input type='submit' value='Submit'&gt;&lt;/td&gt;&lt;/tr&gt;\";\n{$Tab}\$Content .= \"&lt;/table&gt;&lt;/form&gt;\";\n}");
				$Content[]['Text'] = "Rather than simply using echo like I have in previous examples, I put the output into a variable \$Content. This gives me more control over the page I'm working with, since I can just output \$Content later on in my code. When you have to worry about a layout, navigation, and things like that, it helps to keep everything in a variable so you can position it later.";
				$Content[]['Code'] = array("ID"=>"0", "Text"=>"if(!empty(\$_POST))\n{\n{$Tab}\$Author = Clean(\$_POST['Author']);\n{$Tab}\$Comment = Clean(\$_POST['Comment'], \"textarea\");\n\n{$Tab}if(\$Author == \"\")\n{$Tab}{$Tab}\$Errors['Author'] = \"Error: You must enter a name!\";\n{$Tab}elseif(strlen(\$Author) &gt; 32)\n{$Tab}{$Tab}\$Errors['Author'] = \"Error: Your name is too long!\";\n\n{$Tab}if(\$Comment == \"\")\n{$Tab}{$Tab}\$Errors['Comment'] = \"Error: You must write something.\";\n{$Tab}elseif(strlen(\$Comment) &gt; 5000)\n{$Tab}{$Tab}\$Errors['Comment'] = \"Error: Oh come on, your life isn't THAT interesting.\";\n\n{$Tab}if(empty(\$Errors))\n{$Tab}{\n{$Tab}{$Tab}\$Query = mysql_query(\"SELECT `Author`,`Comments` FROM `Posts` WHERE `ID`='\$Go[1]'\");\n{$Tab}{$Tab}list(\$Author, \$Comments) = mysql_fetch_array(\$Query);\n\n{$Tab}{$Tab}if(\$Author == \"\")\n{$Tab}{$Tab}{$Tab}\$Errors['_Global'] = \"Error: This post does not exist!\";\n{$Tab}{$Tab}else\n{$Tab}{$Tab}{\n{$Tab}{$Tab}{$Tab}\$Time = time();\n{$Tab}{$Tab}{$Tab}\$Comments++;\n\n{$Tab}{$Tab}{$Tab}mysql_query(\"INSERT INTO `Comments` VALUES('NULL', '\$Go[1]', '\$Time', '\$Author', '\$Comment')\");\n{$Tab}{$Tab}{$Tab}mysql_query(\"UPDATE `Posts` SET `Comments`='\$Comments' WHERE `ID`='\$Go[1]'\");\n\n{$Tab}{$Tab}{$Tab}\$Content = \"&lt;meta http-equiv='refresh' content='2;url=blog.php?Go=view/\$Go[1]'&gt;Comment successful!\";\n{$Tab}{$Tab}}\n{$Tab}}\n}\n\nif((empty(\$_POST)) || (!empty(\$Errors)))\n{\n{$Tab}\$Content = \"&lt;form method='post'&gt;&lt;table&gt;\";\n\n{$Tab}if(\$Errors['_Global'] != \"\")\n{$Tab}{$Tab}\$Content .= \"&lt;tr&gt;&lt;td colspan='2'&gt;\".\$Errors['_Global'].\"&lt;/td&gt;&lt;/tr&gt;\";\n\n{$Tab}if(\$Errors['Author'] != \"\")\n{$Tab}{$Tab}\$Content .= \"&lt;tr&gt;&lt;td colspan='2'&gt;\".\$Errors['Author'].\"&lt;/td&gt;&lt;/tr&gt;\";\n{$Tab}\$Content .= \"&lt;tr&gt;&lt;td&gt;Author:&lt;/td&gt;&lt;td&gt;&lt;input type='text' name='Author' value='\$Author'&gt;&lt;/td&gt;&lt;/tr&gt;\";\n\n{$Tab}if(\$Errors['Comment'] != \"\")\n{$Tab}{$Tab}\$Content .= \"&lt;tr&gt;&lt;td colspan='2'&gt;\".\$Errors['Comment'].\"&lt;/td&gt;&lt;/tr&gt;\";\n{$Tab}\$Content .= \"&lt;tr&gt;&lt;td&gt;Post:&lt;/td&gt;&lt;td&gt;&lt;textarea name='Comment' rows='4' cols='40'&gt;\$Comment&lt;/textarea&gt;&lt;/td&gt;&lt;/tr&gt;\";\n\n{$Tab}\$Content .= \"&lt;tr&gt;&lt;td colspan='2'&gt;&lt;input type='submit' value='Submit'&gt;&lt;/td&gt;&lt;/tr&gt;\";\n{$Tab}\$Content .= \"&lt;/table&gt;&lt;/form&gt;\";\n}");
				$Content[]['Text'] = "Notice, I added the Error type \"_Global\" to handle if someone was commenting on a post which does not exist. Also, notice how I make sure there are no other errors before checking to see if the page exists. Why is this? Well, no use wasting a query on someone who hasn't entered the data properly! The main reason why websites slow down and crash (like /b/, near a GET) is because there are too many queries happening at once. A database is normally the biggest bottleneck of a website (especially when dealing with separate database servers), where as the processing required to render a webpage can be completed almost instantly.";
				$Content[]['Text'] = "And silly me! I almost forgot, you can grab the post count at the same time when you check to see if the post actually exists, this way, you just have to increment the value and update the database, rather than having to make a second query. Because you already make sure the value is a number, this sort of thing isn't dangerous, but remember, if you ever get a string from the URL and use it in a query, you should always Clean() it!";
				$Content[]['Text'] = "THIS PAGE DOESN'T NEED TO END WITH AN ELLIPSIS!!";

				$Content = Format($Content);
			break;

			case "output":
				$Location = "Blog (Output)";

				$Content[]['Title'] = "Make Your Own... Blog (Final Product)";
				$Content[]['Text'] = "In this final section, I'll explain how to output the data now that we have a method of storing it, and tie it all together into a working package. To start, why not do the main page?";
				$Content[]['Code'] = array("ID"=>"0", "Text"=>"\$Content = \"&lt;a href='?Go=post'&gt;Write a New Post&lt;/a&gt;&lt;hr /&gt;\";\n\n\$Content .= \"&lt;table&gt;\";\n\n\$Query = mysql_query(\"SELECT `ID`,`Time`,`Comments`,`Author`,`Post` FROM `Posts` ORDER BY ID DESC LIMIT 10\");\nwhile(list(\$ID, \$Time, \$Comments, \$Author, \$Post) = mysql_fetch_array(\$Query))\n{\n{$Tab}\$Time = date(\"F j\, Y G:i:s\", \$Time);\n\n{$Tab}if(strlen(\$Post) &gt; 1000)\n{$Tab}{$Tab}\$Post = substr(\$Post, 0, 1000).\"...&lt;br /&gt;(&lt;a href='?Go=view/\$ID'&gt;Read More&lt;/a&gt;)\";\n\n{$Tab}\$Content .= \"&lt;tr&gt;&lt;td&gt;&lt;b&gt;Post by: \$Author&lt;br /&gt;On: \$Time&lt;br /&gt;\";\n{$Tab}\$Content .= \"&lt;a href='?Go=view/\$ID'&gt;Comments: \$Comments&lt;/a&gt;&lt;br /&gt;&lt;/b&gt;&lt;br /&gt;\$Post&lt;hr /&gt;&lt;/td&gt;&lt;/tr&gt;\";\n}\n\n\$Content .= \"&lt;/table&gt;\";");
				$Content[]['Text'] = "Notice the date() function, this converts a unix timestamp into a human-readable date; also, if a post is really long, it snips it a bit. This also isn't the best main page, since it will only show the most recent 10 posts, but it gets the job done. Perhaps in a version you use, you would include a way of having multiple pages.";
				$Content[]['Text'] = "Next up is \"view/\"! But what would it look like?";
				$Content[]['Code'] = array("ID"=>"0", "Text"=>"if(is_numeric(\$Go[1]))\n{\n{$Tab}\$PostQuery = mysql_query(\"SELECT `Time`,`Author`,`Post` FROM `Posts` WHERE `ID`='\$Go[1]'\");\n{$Tab}list(\$Time, \$Author, \$Post) = mysql_fetch_array(\$PostQuery);\n\n{$Tab}\$Time = date(\"F j\, Y G:i:s\", \$Time);\n\n{$Tab}\$Content .= \"&lt;b&gt;Post by: \$Author&lt;br /&gt;On: \$Time&lt;br /&gt;\";\n{$Tab}\$Content .= \"&lt;br /&gt;&lt;/b&gt;&lt;br /&gt;\$Post&lt;/hr&gt;\";\n{$Tab}\$Content .= \"&lt;a href='?Go=post/\$Go[1]'&gt;Comment on this Post&lt;/a&gt;&lt;hr /&gt;\";\n\n{$Tab}\$Content .= \"&lt;table&gt;\";\n\n{$Tab}\$CommentQuery = mysql_query(\"SELECT `Time`,`Author`,`Comment` FROM `Comments` WHERE `PostID`='\$Go[1]'\");\n{$Tab}while(list(\$CommentTime, \$CommentAuthor, \$Comment) = mysql_fetch_array(\$CommentQuery))\n{$Tab}{\n{$Tab}{$Tab}\$CommentTime = date(\"F j\, Y G:i:s\", \$CommentTime);\n\n{$Tab}{$Tab}\$Content .= \"&lt;tr&gt;&lt;td&gt;&lt;b&gt;Comment by: \$Author&lt;br /&gt;On: \$CommentTime&lt;br /&gt;\";\n{$Tab}{$Tab}\$Content .= \"&lt;br /&gt;&lt;/b&gt;&lt;br /&gt;\$Comment&lt;/hr&gt;&lt;/td&gt;&lt;/tr&gt;\";\n{$Tab}}\n\n{$Tab}\$Content .= \"&lt;/table&gt;\";\n}");
				$Content[]['Text'] = "And there you have it! That's it! All! Everything!";
				$Content[]['Text'] = "If you'd like to see a working example, <a href=\"blog.php\">click here</a>. If you'd like to view the full souce, <a href=\"blog.txt\">click here</a>.";

				$Content = Format($Content);
			break;

			default:
				$Location = "Blog (Basics)";

				$Content[]['Title'] = "Make Your Own... Blog (The Basics)";
				$Content[]['Text'] = "Well, well, you've made quite the journey so far! By now you should have a decent grasp of the basics of programming, but perhaps you still feel a void. A lack of real-world examples, perhaps? In these three sections I will try to fill this void by walking you through the process of developing a project, from start to finish!";
				$Content[]['Text'] = "To start, we need to think about what a blog really is. Like a set of LEGOs, a bridge, or anything else you build, a blog is made up of specific parts, and it helps to identify what parts you're going to need before you get building. Then what is a blog made out of? Well, there are posts, and there are comments on those posts. This means you'll first need a way to create these posts, and then a way to view them. Then you'll need a way to comment on those posts, and finally, a way to view those comments.";
				$Content[]['Text'] = "But what is a post? What are the sorts of things you normally see on a blog? An author? The time it was written? The post itself? The number of comments? And don't forget, an ID number! But then, what about comments? They too have an author, a time, the content of the comment, an ID number, and do not forget, a comment must also be associated with a specific post. If you had to express these things as a table in a database, how would it look?";
				$Content[]['Code'] = array("ID"=>"0", "Text"=>"CREATE TABLE `Posts` (\n  `ID` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,\n  `Time` int(10) NOT NULL,\n  `Comments` int(10) NOT NULL,\n  `Author` varchar(32) NOT NULL,\n  `Post` text NOT NULL\n);\n\nCREATE TABLE `Comments` (\n  `ID` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,\n  `PostID` int(10) NOT NULL,\n  `Time` int(10) NOT NULL,\n  `Author` varchar(32) NOT NULL,\n  `Comment` text NOT NULL\n);");
				$Content[]['Text'] = "I normally keep similarly typed columns grouped together, but this isn't necessary.";
				$Content[]['Text'] = "Now that we have our tables, we must think about how we're going to get data in and out of them. How many pages are we going to need? Well, a main page, of course, which lists all of the posts, but how then will we get those posts there in the first place? Perhaps a page called \"post\" would do the trick. Then, once we have the ability to make posts, shouldn't we also be able to view them individually? Maybe \"view/\$PostID\"! Hmm, and now that we can view individual posts, how would we comment on them? Hell, we could use the page we use for making normal blog posts, only with the \$PostID added onto it! And once we can post comments, we should just be able to view them on the \"view/\$PostID\" page!";
				$Content[]['Text'] = "And there you have it, a blog is simply three pages! But what about security? Hmm, if you have a blog, you don't want random people making posts on it! So, I guess there's also going to need to be a password you have to enter when making a blog post...";

				$Content = Format($Content);
			break;
		}
	break;

	case "tutorial":
		$Location = "Tutorial";

		$Content[]['Title'] = "Make Your Own... Tutorial!";
		$Content[]['Text'] = "Oh wow, oh wow. You must be very hungry for knowledge. I kinda want to, like, put your dick in my mouth. Er, I mean, you must want another example of a programming project to learn from! I'm not going to do much explaining, since by this point, I assume you should be able to figure it out on your own, but if you'd like to know how I threw this all together, you can download the source <a href=\"http://wetfish.net/php/index.txt\">here</a>.";
		$Content[]['Text'] = "Writing a tutorial really is pretty fun, believe it or not. It's kind of like a challenge to yourself, to see if you really know something well enough to explain it simply to others. If you're interested in writing your own tutorials, or helping out with an upcoming project, you should get in contact with <a href=\"irc://irc.wetfish.net/wetfish\">someone on irc</a> and ask wtf to do.";
		$Content[]['Text'] = "If you have any questions or comments, once agan, feel free to <a href=\"irc://irc.wetfish.net/wetfish\">yell at someone on IRC</a>!";

		$Content = Format($Content);
	break;

	default:
		$Location = "Introduction";

		$Content[]['Title'] = "An Introduction to PHP";
		$Content[]['Text'] = "Hmm, where to start, where to start.";
		$Content[]['Text'] = "PHP! Yes, this is a PHP tutorial. But what is PHP? Well, it's a recursive acronym, but I won't get into that. More importantly, it's a flexible language you can use to make websites (and a lot of other cool stuff). When you write something in PHP, you generally upload it to a server with a PHP interpreter, then anyone with a web browser and an internet connection should be able to see it!";
		$Content[]['Text'] = "But what do you need to start writing in PHP? Not much, not much at all! Any text editor that lets you save plain text, like notepad or mousepad, will do just fine. You'll also need an FTP client; I generally use <a href=\"http://filezilla-project.org/\" target=\"_blank\">FileZilla</a>, but there are some FireFox addons people have said are just as good. Since this is a Wetfish tutorial, I'm assuming you, the wonderful reader, already have a Wetfish account, which means you already have a server with a PHP interpreter! If you don't have a Wetfish account, you can always <a href=\"irc://irc.wetfish.net/wetfish\">join us on IRC</a> and beg raychjkl for one.";
		$Content[]['Text'] = "With that said, this tutorial assumes you have prior knowledge of basic HTML and CSS. Honestly, if you don't know HTML, it shouldn't make too much of a difference, so long as you feel confident enough in being able to pick it up as you go along. If you really want a place to learn some basic HTML first, check out <a href=\"http://www.w3schools.com/html/html_intro.asp\" target=\"_blank\">W3Schools</a>.";
		$Content[]['Text'] = "Now, let's get started...";

		$Content = Format($Content);
	break;
}

$TableOfContents['Introduction'] = "<a href=\"/php/\">Introduction</a>";
$TableOfContents['Basics'] = "<a href=\"?Go=basics\">Basics</a>"; //variables, arrays, echo, inc/decriment, print_r
$TableOfContents['Blocks'] = "<a href=\"?Go=blocks\">Blocks</a>"; //if, operators (not), elseif, else, switch
$TableOfContents['Loops'] = "<a href=\"?Go=loops\">Loops</a>"; //while, for, foreach
$TableOfContents['Functions'] = "<a href=\"?Go=functions\">Functions</a>"; //writing your own, predefined functions (empty, strlen, is_numeric)
$TableOfContents['Input'] = "<a href=\"?Go=input\">Input</a>"; //$_GET, $_POST, $_COOKIE
$TableOfContents['Error Handling'] = "<a href=\"?Go=errors\">Error Handling</a>"; //Clean(), basics of mysql injection
$TableOfContents['MySQL'] = "<a href=\"?Go=mysql\">MySQL</a>"; //mysql_query(), list(), mysql_fetch_array(), phpmyadmin
$TableOfContents['HowTo'] = "<br /><span style=\"font-size:14pt; font-weight:bold;\">How To Make Your Own...</span>";
$TableOfContents['Blog (Basics)'] = "<a href=\"?Go=blog\">Blog (Basics)</a>"; //What a blog is, what a blog needs
$TableOfContents['Blog (Input)'] = "<a href=\"?Go=blog/input\">Blog (Input)</a>"; //Allowing for user input
$TableOfContents['Blog (Output)'] = "<a href=\"?Go=blog/output\">Blog (Output)</a>"; //Displaying the data
$TableOfContents['Tutorial'] = "<a href=\"?Go=tutorial\">Tutorial</a>"; //OH GOD SO META

if(array_key_exists($Location, $TableOfContents))
	$TableOfContents[$Location] = "<b>$Location</b>";

$TableOfContents = implode("<br />", $TableOfContents);

if($Go[0] != "code")
{
echo <<<HTML
<html>
<head>
<title>The Super Great Wetfish PHP Tutorial!!!</title>


<script language="javascript" type="text/javascript">

var req;

function processReqChange() 
{
	if (req.readyState == 4)
	{
		if (req.status == 200)
		{
			document.getElementById("codewindow").innerHTML = req.responseText;
		}
	}
}

function loadXMLDoc(url) 
{
	var Data;

	 // branch for native XMLHttpRequest object
	if (window.XMLHttpRequest)
	{
		req = new XMLHttpRequest();
		req.onreadystatechange = processReqChange;

		  req.open("GET", url, true);
		  req.send(null);
	 // branch for IE/Windows ActiveX version
	}

	else if (window.ActiveXObject)
	{
		  req = new ActiveXObject("Microsoft.XMLHTTP");
		  if (req) {

			req.onreadystatechange = processReqChange;
				req.open("GET", url, true);
				req.send();
		  }
	 }
}


function PostRequest(url, parameters)
{
	req = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		req = new XMLHttpRequest();
		if (req.overrideMimeType) {
			// set type accordingly to anticipated content type
			//req.overrideMimeType('text/xml');
			req.overrideMimeType('text/html');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			req = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				req = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!req) {
		alert('Cannot create XMLHTTP instance');
		return false;
	}

	req.onreadystatechange = processReqChange;
	req.open('POST', url, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.setRequestHeader("Content-length", parameters.length);
	req.setRequestHeader("Connection", "close");
	req.send(parameters);
}

function GetExample(Example)
{
	if(Example == "get")
	{
		var CodeID = 18;

		var Address = document.getElementById("Address").value;
		var Data = Address.split("?");

		var UsableData = encodeURI(Data[1]);
	}

	if(Example == "post")
	{
		var CodeID = 19;

		var UsableData = "Name=" + encodeURI(document.getElementById("Name").value) + "&Password=" + encodeURI(document.getElementById("Password").value);
	}

	if(Example == "errors")
	{
		var CodeID = 21;

		var UsableData = "Name=" + encodeURI(document.getElementById("Name").value) + "&Age=" + encodeURI(document.getElementById("Age").value);
	}

	if(Example == "shoutbox")
	{
		var CodeID = 23;

		var UsableData = "Name=" + encodeURI(document.getElementById("Name").value) + "&Post=" + encodeURI(document.getElementById("Post").value);
	}

	PostRequest("http://wetfish.net/php/?Go=code/" + CodeID, UsableData);
}

function OpenCode(CodeID)
{
	if(CodeID != 0)
	{
		loadXMLDoc('http://wetfish.net/php/?Go=code/' + CodeID);

		document.getElementById("popupbox").style.display = "";
	}
}

function CloseCode()
{
	document.getElementById("popupbox").style.display = "none";
	document.getElementById("codewindow").innerHTML = "";
}

</script>

<style type="text/css">
body { background:#18395B; color: #FFFFFF; font-family: Tahoma,Helvetica,Sans-serif; font-size:10pt; margin-left:10%; margin-right:10%; }
a { color:#98b3cd; text-decoration: none; }
a:hover { color:#98b3cd; text-decoration: underline; }

div.code { display: table; padding-left: 8px; }
div.code:hover { background:#366290; }

div.background { position:fixed; top:0; left:0; background:#000000; width:100%; height:100%; filter:alpha(opacity=25);-moz-opacity:.25;opacity:.25; }
div.codecontainer { float: left; position: fixed; text-align: right; top:25%; left:25%; right:25%; bottom:25%; } 
div.codewindow { overflow:auto;text-align: left; width:100%; height:100%; padding: 8px; background:#FFFFFF; color: #000000; border: 8px solid #366290; }

</style>

</head>
<body>

<div id="popupbox" style="display:none;">
<div class="background">&nbsp;</div>
<div class="codecontainer">[<a href="javascript:CloseCode()">Close Window</a>]<div id="codewindow" class="codewindow">&nbsp;</div></div>
</div>

$Content
<hr />
$TableOfContents
<hr />
<a href="http://wetfish.net/">wetfish.net</a> &mdash; The development community.<br />
<a href="http://anonimap.info/">anonimap.info</a> &mdash; The globally local imageboard.
</body>
</html>
HTML;
}
?>
