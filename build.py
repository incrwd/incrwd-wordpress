import subprocess
import getopt
import shutil
import re
import os
import sys

PROJECT_ROOT = os.path.abspath(os.path.dirname(__file__))

def _rel(*x):
    return os.path.join(PROJECT_ROOT, *x)

def _run(cmd):
    print("Running {0}".format(" ".join(cmd)))
    process = subprocess.Popen(
        cmd,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE)
    output, err = process.communicate()
    print(output)
    print(err)

def _find_version():
    with open(_rel('incrwd/incrwd.php')) as f:
        text = f.read()
    return re.search(r"Version:\s*([\d\.]+)", text).group(1)

def _copy_build(local_only):
    file_name = "incrwd/build-{0}.php".format(
        "local" if local_only else "production")
    shutil.copy(_rel(file_name), _rel("incrwd/build.php"))

def _zip(version):
    _run(["zip", "-r",
          _rel("build/incrwd-{0}.zip".format(version)),
          "incrwd",
          "-x", "*~"])

def _main(argv):
    opts, args = getopt.getopt(argv, "r", ["release"])
    local_only = True
    for opt, arg in opts:
        if opt in ("-r", "--release"):
            local_only = False
    print("You are running the {0} deployment.\n".format(
            "local" if local_only else "production"))
    version = _find_version()
    _copy_build(local_only)
    _zip(version)


_main(sys.argv[1:])
