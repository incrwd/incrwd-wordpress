import subprocess
import getopt
import shutil
import re
import os
import sys

PROJECT_ROOT = os.path.abspath(os.path.dirname(__file__))

def _rel(*x):
    return os.path.join(PROJECT_ROOT, *x)

def _run(cmd, cwd=None):
    print("Running {0}".format(" ".join(cmd)))
    process = subprocess.Popen(
        cmd,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        cwd=cwd)
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

def _readme_version(file_name):
    with open(file_name) as f:
        text = f.read()
    version_string = \
        re.search(r"Stable tag:\s*([\d\.]+)", text).group(1)
    return [int(x) for x in version_string.split('.')]

def _svn_needs_update(svn_dir):
    git_version = _readme_version(_rel('incrwd/readme.txt'))
    svn_version = _readme_version(os.path.join(svn_dir, 'trunk/readme.txt'))
    comp = cmp(git_version, svn_version)
    if comp == 1:
        print(("Got a git version of {0} and svn "
               "version of {1}. Updating!").format(
                git_version, svn_version))
    return comp

def _get_svn_password():
    import build_settings
    return build_settings.svn_password

def _readme_string(version_array):
    return '.'.join(str(v) for v in version_array)

def _copy_to_svn_dst(src, dst, added_files):
    names = os.listdir(src)
    if not os.path.exists(dst):
        os.makedirs(dst)
    for name in names:
        if name[-1] == '~':
            continue
        srcname = os.path.join(src, name)
        dstname = os.path.join(dst, name)
        if os.path.isdir(srcname):
            _copy_to_svn_dst(srcname, dstname, added_files)
        else:
            preexisting = os.path.exists(dstname)
            shutil.copy2(srcname, dstname)
            if not preexisting:
                added_files.append(dstname)

def _update_svn(svn_dir):
    svn_password = _get_svn_password()
    svn_rel = lambda *x: os.path.join(svn_dir, *x)
    version = _readme_string(_readme_version(_rel('incrwd/readme.txt')))
    added_files = []
    _copy_to_svn_dst(_rel('incrwd'), svn_rel('trunk'), added_files)
    for file in added_files:
        _run(['svn', 'add', file], cwd=svn_dir)
    _run(['svn', 'cp', svn_rel('trunk'), svn_rel('tags', version)], 
         cwd=svn_dir)
    _run(['svn', 'ci', '-m', 'Version {0}'.format(version), 
          '--username', 'incrwd', '--password', svn_password],
         cwd=svn_dir)

def _main(argv):
    opts, args = getopt.getopt(argv, "rs:", ["release", "svndir"])
    local_only = True
    git_dir = None
    svn_dir = None
    for opt, arg in opts:
        if opt in ("-r", "--release"):
            local_only = False
        elif opt in ("-s", "--svndir"):
            svn_dir = arg
    if svn_dir is None:
        raise Exception("The -s arguments isn't optional, homeboy!")
    print("You are running the {0} deployment.\n".format(
            "local" if local_only else "production"))
    version = _find_version()
    _copy_build(local_only)
    _zip(version)
    if _svn_needs_update(svn_dir):
        _update_svn(svn_dir)

_main(sys.argv[1:])
