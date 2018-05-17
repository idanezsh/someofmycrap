#! /usr/bin/python --

"""

Goto  http://www.redwoodsoft.com/~dru/mailRemove/ 
 For Info and Docs

 restore.py - this restores the files in the filter
 directory to their original place

This is a program by Dru Nelson. You can contact me
and discuss bugs/issues/kudos at dru@redwoodsoft.com.

20030707 - milagros Alva <milagrosalva@hotmail.com> - needed this script


/var/qmail/queue

Hashed: info mess local remote
Not Hashed: intd todo bounce

"""


import os, sys, string, time, getopt

n        = 0
ForReal  = 0


def usage(progname):
  print "usage: %s" % progname
  print
  print
  print "This program will properly restore mail a mailRemove filter directory"
  print "Mail will not be REALLY restored until --real is used as"
  print "an option."
  print
  print "--help and --real "
  print "-- and --real --file=xyz"
  print "Examples:"
  print "./restore.py"
  print "./restore.py --real"
  sys.exit(1)

def hashPath(dir, fileName):
  p = '/var/qmail/queue/%s/%s/%s' % (dir,  ( int(fileName) % 23 ), fileName)
  return p

def messPath(fileName):
  return hashPath('mess', fileName)

def localPath(fileName):
  return hashPath('local', fileName)

def remotePath(fileName):
  return hashPath('remote', fileName)

def infoPath(fileName):
  return hashPath('info', fileName)

def intdPath(fileName):
  p = '/var/qmail/queue/intd/%s' % (fileName)
  return p

def todoPath(fileName):
  p = '/var/qmail/queue/todo/%s' % (fileName)
  return p

def bouncePath(fileName):
  p = '/var/qmail/queue/bounce/%s' % (fileName)
  return p


def processFile(fileName):
  
  spamPath = '/var/qmail/queue/filter'

  path = spamPath + '/%s' % (fileName)

  name,ext = string.split(fileName, ".")
  
  if ext == "mess":
     print "Renaming Mess [%s] -> [%s]" % (path, messPath(name))
     if ForReal == 1:
        os.rename(path, messPath(name))
     return

  if ext == "info":
     print "Renaming Info [%s] -> [%s]" % (path, infoPath(name))
     if ForReal == 1:
        os.rename(path, infoPath(name))
     return

  if ext == "local":
     print "Renaming Local [%s] -> [%s]" % (path, localPath(name))
     if ForReal == 1:
        os.rename(path, localPath(name))
     return

  if ext == "remote":
     print "Renaming Remote [%s] -> [%s]" % (path, remotePath(name))
     if ForReal == 1:
        os.rename(path, remotePath(name))
     return

  if ext == "intd":
     print "Renaming intd [%s] -> [%s]" % (path, intdPath(name))
     if ForReal == 1:
        os.rename(path, intdPath(name))
     return

  if ext == "todo":
     print "Renaming Todo [%s] -> [%s]" % (path, todoPath(name))
     if ForReal == 1:
        os.rename(path, todoPath(name))
     return

  if ext == "bounce":
     print "Renaming Bounce [%s] -> [%s]" % (path, bouncePath(name))
     if ForReal == 1:
        os.rename(path, bouncePath(name))
     return


def main(argv, stdout, environ):
  global ForReal
  global MatchAny

  inputFile = None

  progname = argv[0]
  list, args = getopt.getopt(argv[1:], "", ["real","help",])

  wordList = args

  for (field, val) in list:
    if field == "--help":
      usage(progname)
      return
    if field == "--real":
      ForReal = 1
    else:
      if not ForReal:
	      print
	      print ' **** Not FOR REAL **** '
	      print

  if os.getuid() != 0:
    print "Must be run as root"
    sys.exit(1)

  if not os.path.exists('/var/qmail/queue/filter'):
    print 'This program requires a directory called /var/qmail/queue/filter'
    print 'This is where the filtered mail is expected to be.'
    print 'Exiting.'
    sys.exit(1)

  #
  # Go through all of the local deliveries
  #
  path = "/var/qmail/queue/filter"
  filenames = os.listdir(path)
  print '\n[' + path + ']'
  i = 0

  for fn in filenames:
      i = i + 1
      processFile(fn)

  print
  if not ForReal:
	  print ""
	  print "END OF TRIAL RUN - Not Run with --real option"
	  print ""
  print
  print ' Total: %s ' % (i)
  print 

if __name__ == "__main__":
  main(sys.argv, sys.stdout, os.environ)
