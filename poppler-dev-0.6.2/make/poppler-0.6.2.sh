# This is a shell script that is sourced, not executed. It uses
# functions and scripts from tml@iki.fi's work envíronment and is
# included only for reference

MOD=poppler
VER=0.6.2
THIS=$MOD-$VER
HEX=`echo $THIS | md5sum | cut -d' ' -f1`
DEPS=`/devel/src/tml/latest.sh atk cairo fontconfig freetype glib gtk+ libglade pango`
sed -e 's/need_relink=yes/need_relink=no # no way --tml/' <ltmain.sh >ltmain.temp && mv ltmain.temp ltmain.sh
# poppler's configury looks for jpeglib.h in hardcoded locations, sigh
# add /opt/gnuwin32/include to those
sed -e 's!jpeg_incdirs="!&/opt/gnuwin32/include !'<configure >configure.temp && mv configure.temp configure
sed -e 's!INCLUDES =!& -DLIBPOPPLER_COMPILATION!' <poppler/Makefile.in >poppler/Makefile.in.temp && mv poppler/Makefile.in.temp poppler/Makefile.in

patch -p0 <<'EOF'
--- poppler/GlobalParams.cc~	2007-11-05 01:11:04.000000000 +0200
+++ poppler/GlobalParams.cc	2007-11-22 21:34:33.959200600 +0200
@@ -22,6 +22,7 @@
 #endif
 #ifdef WIN32
 #  include <shlobj.h>
+#  include <mbstring.h>
 #endif
 #include <fontconfig/fontconfig.h>
 #include "goo/gmem.h"
@@ -76,6 +77,59 @@
 #  endif
 #endif
 
+#ifdef WIN32
+static HMODULE hmodule;
+
+extern "C" {
+BOOL WINAPI
+DllMain (HINSTANCE hinstDLL,
+	 DWORD     fdwReason,
+	 LPVOID    lpvReserved)
+{
+  switch (fdwReason)
+    {
+    case DLL_PROCESS_ATTACH:
+      hmodule = hinstDLL;
+      break;
+    }
+
+  return TRUE;
+}
+}
+
+static char *
+get_poppler_datadir (void)
+{
+  static char retval[1000];
+  static int beenhere = 0;
+
+  unsigned char *p;
+
+  if (beenhere)
+    return retval;
+
+  if (!GetModuleFileName (hmodule, (CHAR *) retval, sizeof(retval) - 10))
+    return POPPLER_DATADIR;
+
+  p = _mbsrchr ((const unsigned char *) retval, '\\');
+  *p = '\0';
+  p = _mbsrchr ((const unsigned char *) retval, '\\');
+  if (p) {
+    if (stricmp ((const char *) (p+1), "bin") == 0)
+      *p = '\0';
+  }
+  strcat (retval, "\\share\\poppler");
+
+  beenhere = 1;
+
+  return retval;
+}
+
+#undef POPPLER_DATADIR
+#define POPPLER_DATADIR get_poppler_datadir ()
+
+#endif
+
 //------------------------------------------------------------------------
 
 #define cidToUnicodeCacheSize     4
@@ -653,8 +707,11 @@
 void GlobalParams::scanEncodingDirs() {
   GDir *dir;
   GDirEntry *entry;
+  char dirname[1000];
 
-  dir = new GDir(POPPLER_DATADIR "/nameToUnicode", gTrue);
+  strcpy(dirname, POPPLER_DATADIR);
+  strcat(dirname, "/nameToUnicode");
+  dir = new GDir(dirname, gTrue);
   while (entry = dir->getNextEntry(), entry != NULL) {
     if (!entry->isDir()) {
       parseNameToUnicode(entry->getFullPath());
@@ -663,21 +720,27 @@
   }
   delete dir;
 
-  dir = new GDir(POPPLER_DATADIR "/cidToUnicode", gFalse);
+  strcpy(dirname, POPPLER_DATADIR);
+  strcat(dirname, "/cidToUnicode");
+  dir = new GDir(dirname, gFalse);
   while (entry = dir->getNextEntry(), entry != NULL) {
     addCIDToUnicode(entry->getName(), entry->getFullPath());
     delete entry;
   }
   delete dir;
 
-  dir = new GDir(POPPLER_DATADIR "/unicodeMap", gFalse);
+  strcpy(dirname, POPPLER_DATADIR);
+  strcat(dirname, "/unicodeMap");
+  dir = new GDir(dirname, gFalse);
   while (entry = dir->getNextEntry(), entry != NULL) {
     addUnicodeMap(entry->getName(), entry->getFullPath());
     delete entry;
   }
   delete dir;
 
-  dir = new GDir(POPPLER_DATADIR "/cMap", gFalse);
+  strcpy(dirname, POPPLER_DATADIR);
+  strcat(dirname, "/cMap");
+  dir = new GDir(dirname, gFalse);
   while (entry = dir->getNextEntry(), entry != NULL) {
     addCMapDir(entry->getName(), entry->getFullPath());
     toUnicodeDirs->append(entry->getFullPath()->copy());
EOF

patch -p0 <<'EOF'
--- glib/demo/info.c~	2007-11-05 01:11:00.000000000 +0200
+++ glib/demo/info.c	2007-11-22 20:38:38.568575600 +0200
@@ -21,6 +21,10 @@
 #include "info.h"
 #include "utils.h"
 
+#ifdef G_OS_WIN32
+#define localtime_r(tp,tmp) (localtime(tp) ? (*(tmp) = *localtime (tp), (tmp)) : NULL)
+#endif
+
 static gchar *
 poppler_format_date (GTime utime)
 {
EOF

patch -p0 <<'EOF'
--- glib/Makefile.in~	2007-11-10 14:04:33.000000000 +0200
+++ glib/Makefile.in	2007-11-22 20:43:40.006075600 +0200
@@ -330,7 +330,7 @@
 	$(FONTCONFIG_LIBS)				\
 	$(cairo_libs)
 
-libpoppler_glib_la_LDFLAGS = -version-info 2:0:0
+libpoppler_glib_la_LDFLAGS = -version-info 2:0:0 @create_shared_lib@
 test_poppler_glib_SOURCES = \
        test-poppler-glib.cc
 
EOF

usedev

unset MY_PKG_CONFIG_PATH
for D in $DEPS; do
    PATH=/devel/dist/$D/bin:$PATH
    MY_PKG_CONFIG_PATH=/devel/dist/$D/lib/pkgconfig:$MY_PKG_CONFIG_PATH
done

PKG_CONFIG_PATH=$MY_PKG_CONFIG_PATH:$PKG_CONFIG_PATH CC='gcc -mtune=pentium3' CPPFLAGS='-I/opt/misc/include -I/opt/gnuwin32/include' LDFLAGS='-L/opt/misc/lib -L/opt/gnuwin32/lib -L/opt/gnu/lib -Wl,--enable-runtime-pseudo-reloc' CFLAGS=-O2 ./configure --disable-static --enable-zlib --disable-abiword-output --disable-cairo-output --prefix=c:/devel/target/$HEX &&
libtoolcacheize &&
PATH=/devel/target/$HEX/bin:$PATH make -j3 install &&
(cd /devel/target/$HEX &&
zip /tmp/$MOD-$VER.zip bin/*.dll) &&
(cd /devel/target/$HEX &&
zip /tmp/$MOD-dev-$VER.zip bin/*.exe &&
zip -r /tmp/$MOD-dev-$VER.zip include &&
zip /tmp/$MOD-dev-$VER.zip lib/*.dll.a &&
zip -r /tmp/$MOD-dev-$VER.zip lib/pkgconfig && 
zip -r /tmp/$MOD-dev-$VER.zip share) &&
(cd /devel/src/tml && zip /tmp/$MOD-dev-$VER.zip make/$THIS.sh) &&
manifestify /tmp/$MOD-$VER.zip /tmp/$MOD-dev-$VER.zip
