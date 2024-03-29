/* ************************************************************
Created: 20060120
Author:  Steve Moitozo <god at zilla dot us>
Description: This is a quick and dirty password quality meter 
         written in JavaScript so that the password does 
         not pass over the network
Revision Author: Dick Ervasti (dick dot ervasti at quty dot com)
Revision Description: Exchanged text based prompts for a graphic thermometer
---------------------------------------------------------------
Copyright (c) 2006 Steve Moitozo <god at zilla dot us>

Permission is hereby granted, free of charge, to any person 
obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without 
restriction, including without limitation the rights to use, 
copy, modify, merge, publish, distribute, sublicense, and/or 
sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following 
conditions:

The above copyright notice and this permission notice shall 
be included in all copies or substantial portions of the 
Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY 
KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE 
AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE 
OR OTHER DEALINGS IN THE SOFTWARE. 
---------------------------------------------------------------

Password Strength Factors and Weightings

password length:
level 0 (3 point): less than 4 characters
level 1 (6 points): between 5 and 7 characters
level 2 (12 points): between 8 and 15 characters
level 3 (18 points): 16 or more characters

letters:
level 0 (0 points): no letters
level 1 (5 points): all letters are lower case
level 2 (7 points): letters are mixed case

numbers:
level 0 (0 points): no numbers exist
level 1 (5 points): one number exists
level 1 (7 points): 3 or more numbers exists

special characters:
level 0 (0 points): no special characters
level 1 (5 points): one special character exists
level 2 (10 points): more than one special character exists

combinatons:
level 0 (1 points): letters and numbers exist
level 1 (1 points): mixed case letters
level 1 (2 points): letters, numbers and special characters 
                    exist
level 1 (2 points): mixed case letters, numbers and special 
                    characters exist


NOTE: Because I suck at regex the code below is incomplete and 
      does not accurately assess the strength of passwords 
      according to the above factors and weightings
      
NOTE: Instead of putting out all the logging information,
      the score, and the verdict it would be nicer to stretch
      a graphic as a method of presenting a visual strength
      guage.

************************************************************ */
function testPassword(passwd)
{
var description = new Array();
description[0] = "<table><tr><td>&nbsp;Weakest</td></tr><tr><td><table cellpadding=0 cellspacing=2><tr><td height=4 width=30 bgcolor=#ff0000></td><td height=4 width=120 bgcolor=tan></td></tr></table></td></tr></table>";
description[1] = "<table><tr><td>&nbsp;Weak</td></tr><tr><td><table cellpadding=0 cellspacing=2><tr><td height=4 width=60 bgcolor=#990000></td><td height=4 width=90 bgcolor=tan></td></tr></table></td></tr></table>";
description[2] = "<table><tr><td>&nbsp;Improving</td></tr><tr><td><table cellpadding=0 cellspacing=2><tr><td height=4 width=90 bgcolor=#990099></td><td height=4 width=60 bgcolor=tan></td></tr></table></td></tr></table>";
description[3] = "<table><tr><td>&nbsp;Strong</td></tr><tr><td><table cellpadding=0 cellspacing=2><tr><td height=4 width=120 bgcolor=#000099></td><td height=4 width=30 bgcolor=tan></td></tr></table></td></tr></table>";
description[4] = "<table><tr><td>&nbsp;Strongest</td></tr><tr><td><table cellpadding=0 cellspacing=2><tr><td height=4 width=150 bgcolor=#0000ff></td></tr></table></td><tr></table>";
description[5] = "<table><tr><td>&nbsp;Begin Typing</td></tr><tr><td><table cellpadding=0 cellspacing=2><tr><td height=4 width=150 bgcolor=tan></td></tr></table></td><tr></table>";


        var intScore   = 0
        var strVerdict = 0
        
        // PASSWORD LENGTH
        if (passwd.length==0 || !passwd.length)                         // length 0
        {
            intScore = -1
        }
        else if (passwd.length>0 && passwd.length<5) // length between 1 and 4
        {
            intScore = (intScore+3)
        }
        else if (passwd.length>4 && passwd.length<8) // length between 5 and 7
        {
            intScore = (intScore+6)
        }
        else if (passwd.length>7 && passwd.length<12)// length between 8 and 15
        {
            intScore = (intScore+12)
        }
        else if (passwd.length>11)                    // length 16 or more
        {
            intScore = (intScore+18)
        }
        
        
        // LETTERS (Not exactly implemented as dictacted above because of my limited understanding of Regex)
        if (passwd.match(/[a-z]/))                              // [verified] at least one lower case letter
        {
            intScore = (intScore+1)
        }
        
        if (passwd.match(/[A-Z]/))                              // [verified] at least one upper case letter
        {
            intScore = (intScore+5)
        }
        
        // NUMBERS
        if (passwd.match(/\d+/))                                 // [verified] at least one number
        {
            intScore = (intScore+5)
        }
        
        if (passwd.match(/(.*[0-9].*[0-9].*[0-9])/))             // [verified] at least three numbers
        {
            intScore = (intScore+5)
        }
        
        
        // SPECIAL CHAR
        if (passwd.match(/.[!,@,#,$,%,^,&,*,?,_,~]/))            // [verified] at least one special character
        {
            intScore = (intScore+5)
        }
        
                                                                 // [verified] at least two special characters
        if (passwd.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/))
        {
            intScore = (intScore+5)
        }
    
        
        // COMBOS
        if (passwd.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))        // [verified] both upper and lower case
        {
            intScore = (intScore+2)
        }

        if (passwd.match(/(\d.*\D)|(\D.*\d)/))                    // [FAILED] both letters and numbers, almost works because an additional character is required
        {
            intScore = (intScore+2)
        }
 
                                                                  // [verified] letters, numbers, and special characters
        if (passwd.match(/([a-zA-Z0-9].*[!,@,#,$,%,^,&,*,?,_,~])|([!,@,#,$,%,^,&,*,?,_,~].*[a-zA-Z0-9])/))
        {
            intScore = (intScore+2)
        }
    
    
        if(intScore == -1)
        {
           strVerdict = description[5];
        }
        else if(intScore > -1 && intScore < 16)
        {
           strVerdict = description[0];
        }
        else if (intScore > 15 && intScore < 25)
        {
           strVerdict = description[1];
        }
        else if (intScore > 24 && intScore < 35)
        {
           strVerdict = description[2];
        }
        else if (intScore > 34 && intScore < 45)
        {
           strVerdict = description[3];
        }
        else
        {
           strVerdict = description[4];
        }
    
    document.getElementById("pwStrength").innerHTML= (strVerdict);
    
}
