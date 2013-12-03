#!/usr/bin/python

import MySQLdb
import urllib2
import json

def main():
	for i in xrange(1600000,2000000):
		getInfo("tt{0}".format(str(i).zfill(7)))

def getInfo(id):
	try:
		data = urllib2.urlopen('http://www.omdbapi.com/?i={0}'.format(id))
	except urllib2.URLError, e:
		print u"Error: {0}\nTrying again...".format(e)
		try:
			data = urllib2.urlopen('http://www.omdbapi.com/?i={0}'.format(id))
		except urllib2.URLError, e:
			print u"Error: {0}\nSkipping.".format(e)
			return 1
	try:
		j = json.load(data)
	except ValueError:
		return 6
	if j.has_key('Error'):
		pass
	if j.has_key('Title'):
		print u"Inserting {0} - {1}".format(id, j['Title'])
		insert(id, j['Title'], j['Year'], j['imdbRating'], j['Released'], j['Genre'], j['Plot'], j['Poster'])

def insert(id, title, year, rating, released, genre, plot, posterurl):
	conn = MySQLdb.connect(host= "localhost",
							user="username",
							passwd="password",
							db="files")
	x = conn.cursor()

	try:
		x.execute("""INSERT INTO imdb VALUES (%s,%s,%s,%s,%s,%s,%s,%s)""",(id, title, year, rating, released, genre, plot, posterurl))
		conn.commit()
	except:
		conn.rollback()
	
	conn.close()

if __name__ == "__main__":
	main()
