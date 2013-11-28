#!/usr/bin/python
import os, re
from re import sub
import urllib2, urllib
import json
import time

def main1():
	fileExts = ['.avi', '.mkv', '.mp4']
	# List directories
	for moviename in os.listdir("."):
		# If it's a directory
		if os.path.isdir(moviename):
			# Get a list of files within the directory
			for f in os.listdir(moviename):
				# If it's a file
				if os.path.isfile(os.path.join(moviename,f)):
					# If the file has the correct extension and does not contain the string 'sample'
					if os.path.splitext(os.path.join(moviename,f))[1] in fileExts and not re.search('sample', f, re.IGNORECASE):
						# Make an attempt to find the year
						try:
							# '(?i)([12]\d{3})[^p]' Case insensitive, starting with 1 or 2 and 3 more digits not ending with p (1080p)
							releaseYear = re.search(r'(?i)([12]\d{3})[^p]', os.path.join(moviename,f)).groups()[0]
						except AttributeError:
							releaseYear = ''
						#print genFilename(f)
						filmTitle = genFilename(f)
						imdbID = getID( filmTitle, releaseYear )
						filepath = os.path.join(moviename,f)
						insertURL = "http://linnit.pw/?a=insert&title={0}&type=Movie&id={1}&path={2}&date={3}".format(filmTitle, imdbID, filepath, time.strftime("%Y-%m-%d"))
						urllib2.urlopen(insertURL)
						#print "{0} - {1} - {2}" .format(filmTitle, imdbID, os.path.join(moviename,f))

def main():
	#getID()
	main1()

def getID(searchTerm, releaseYear):
	searchTerm = urllib.quote_plus(searchTerm)
	data = urllib2.urlopen('http://www.omdbapi.com/?s={0}&y={1}'.format(searchTerm, releaseYear))
	j = json.load(data)
	if j.has_key('Error'):
		print "Nothing found for {0}".format(searchTerm)
		return 0
	for a in j['Search']:
		if a['Type'] == 'movie':
			return a['imdbID']
			#print u"{0} - {1} - {2}".format(searchTerm, a['Title'], a['imdbID'])
			return 0

def genFilename(in_name): # Generate a cleaner filename
	extension = in_name[-4:] # Save extension for later
	in_name = in_name[:-4] # Removes extension
	
	# Split the name by the first bracket, and replace all '.' with spaces
	out = in_name.split('[')[0].replace('.', ' ')
	
	# Remove common parts of names
	# The '.*' also removes everything after, since it's normally all useless,
	# but the extension has already been saved so it's not needed
	regex = '(?i)(brrip.*|bdrip.*|xvid.*|hdrip.*|x264.*|720p.*|1080p.*|dvdrip.*|bluray.*|hdtv.*|extended.*|unrated|refined|noscr|remastered|uncut)'
	out = sub(regex, '', out)
	
	out = sub('(-|_)', ' ', out) # Replace _ or - with a space
	out = sub('[12]\d{3}', ' ', out) # Remove year
	out = sub('R\d', ' ', out) # Remove R5/R6/R7 etc
	out = sub('\(.+\)', ' ', out)
	out = sub('(\ +)', ' ', out) # Change all spaces to a single space character
	out = sub('(\ +$)', '', out) # Remove trailing spaces
	out = sub('(^\s+)', '', out) # Remove leading spaces
	#filename = sub('[12]\d{3}', '', f)
	out = out.title()
	
	return out#, extension

if __name__ == "__main__":
	main()
