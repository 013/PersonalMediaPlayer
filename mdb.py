#!/usr/bin/python

import MySQLdb
import urllib2
import json

def main():
	for i in xrange(110000,9999999):
		getInfo("tt{0}".format(str(i).zfill(7)))

def getInfo(id):
	data = urllib2.urlopen('http://www.omdbapi.com/?i={0}'.format(id))
	j = json.load(data)
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
