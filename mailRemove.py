#! /usr/bin/python --

"""

Goto  http://www.redwoodsoft.com/~dru/mailRemove/ 
 For Info and Docs


This is a program by Dru Nelson. You can contact me
and discuss bugs/issues/kudos at dru@redwoodsoft.com.

Go through the queue directories and then rename the related
files to a 'filter' directory

20030204 - jmvw@spamcop.net - included queue/bounce dir
20020820 - DN - removing lots of bounces
20000115 - DN - OneList Move - removing mail bomb
19991230 - DN - Making this work


/var/qmail/queue

Hashed: info mess local remote
Not Hashed: intd todo bounce

"""


import os, sys, string, time, getopt

n        = 0
ForReal  = 0
MatchAny = 0

def usage(progname):
  print "usage: %s" % progname
  print __doc__


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


def messMatch(fileName, matchList):
  
  try:
    f = open(messPath(fileName), "r")
  except:
    print "Couldn't open [%s] Skipping" % messPath(fileName)
    return 0

  data = f.read()
  f.close()

  i = 0
  for match in matchList:
    match = string.find(data, match)
    if MatchAny:
      if match != -1:
        print "Matched Any [%s] with [%s]" % (fileName, match)
        return 1
    else:
      if match != -1:
        i = i + 1
      else:
        return

  if i == len(matchList):
    print "Matched [%s]" % fileName
    return 1
  else:
    return 0


def processFile(fileName, matchList):
  
  spamPath = '/var/qmail/queue/filter'

  path = spamPath + '/%s.mess' % (fileName)

  if os.path.exists(path):
    print " STRANGE -> %s already exists" % path

  print "Renaming Mess [%s] -> [%s]" % (messPath(fileName), path)
  if ForReal == 1:
    os.rename(messPath(fileName), path)
     
  # Local
  if os.path.exists(localPath(fileName)):
    path = spamPath + '/%s.local' % (fileName)
    print "Renaming Local [%s] -> [%s]" % (localPath(fileName), path)
    if ForReal == 1:
      os.rename(localPath(fileName), path)

  # Remote
  if os.path.exists(remotePath(fileName)):
    path = spamPath + '/%s.remote' % (fileName)
    print "Renaming Remote [%s] -> [%s]" % (remotePath(fileName), path)
    if ForReal == 1:
      os.rename(remotePath(fileName), path)

  # Info
  if os.path.exists(infoPath(fileName)):
    path = spamPath + '/%s.info' % (fileName)
    print "Renaming Info [%s] -> [%s]" % (infoPath(fileName), path)
    if ForReal == 1:
      os.rename(infoPath(fileName), path)

  # Intd
  if os.path.exists(intdPath(fileName)):
    path = spamPath + '/%s.intd' % (fileName)
    print "Renaming Intd [%s] -> [%s]" % (intdPath(fileName), path)
    if ForReal == 1:
      os.rename(intdPath(fileName), path)

  # Todo
  if os.path.exists(todoPath(fileName)):
    path = spamPath + '/%s.todo' % (fileName)
    print "Renaming Todo [%s] -> [%s]" % (todoPath(fileName), path)
    if ForReal == 1:
      os.rename(todoPath(fileName), path)

  # Bounce
  if os.path.exists(bouncePath(fileName)):
    path = spamPath + '/%s.bounce' % (fileName)
    print "Renaming Bounce [%s] -> [%s]" % (bouncePath(fileName), path)
    if ForReal == 1:
      os.rename(bouncePath(fileName), path)
  




def main(argv, stdout, environ):
  global ForReal
  global MatchAny

  inputFile = None

  progname = argv[0]
  list, args = getopt.getopt(argv[1:], "", ["real","help","matchany", "file="])

  wordList = args

  for (field, val) in list:
    if field == "--help":
      usage(progname)
      return
    if field == "--real":
      ForReal = 1
    if field == "--matchany":
      MatchAny = 1
    if field == "--file":
      inputFile = val
    else:
      if not ForReal:
	      print
	      print ' **** Not FOR REAL **** '
	      print

  if os.getuid() != 0:
    print "Must be run as root"
    sys.exit(1)

  if len(args) == 0 and inputFile == None:
    print
    print
    print "This program will remove mail 'properly' from a qmail queue"
    print "Mail will not be REALLY removed until --real is used as"
    print "an option."
    print
    print "Requires arguments for matcing words or a file with the words"
    print "--help and --real --file=xyz"
    print "-- and --real --file=xyz"
    print "Examples:"
    print "./mailRemove.py annacsfun puyals98"
    print "./mailRemove.py --real annacsfun puyals98"
    sys.exit(1)

  if not os.path.exists('/var/qmail/queue/filter'):
    print 'This program requires a directory called /var/qmail/queue/filter'
    print 'Please create it. This is where the filtered mail will be placed'
    print 'Exiting.'
    sys.exit(1)

  if inputFile != None:
    wordList = []
    f = open(inputFile, 'r')
    while 1:
      word = f.readline()
      if word == '':
        break
      word = word[:-1]
      wordList.append(word)
    f.close()
    
  for z in wordList:
    print "[ %s ]" % z

  ##sys.exit(0)
  #
  # Go through all of the local deliveries
  #
  path = "/var/qmail/queue/mess"
  dirs = os.listdir(path)
  print '\n[' + path + ']'
  i = 0

  for dir in dirs:
    queuepath = os.path.join('/var/qmail/queue/mess', dir)
    files = os.listdir(queuepath)
    print "Entering [%s]" % queuepath
    for f in files:
      if messMatch( f, wordList) == 1:
        i = i + 1
        processFile( f, wordList)

  print
  if not ForReal:
	  print "END OF TRIAL RUN - Not Run with --real option"
	  print "END OF TRIAL RUN - Not Run with --real option"
	  print "END OF TRIAL RUN - Not Run with --real option"
  print
  print ' Total: %s ' % (i)
  print 

if __name__ == "__main__":
  main(sys.argv, sys.stdout, os.environ)
